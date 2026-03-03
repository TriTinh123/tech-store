<?php

namespace App\Http\Controllers;

use App\Models\IncidentCase;
use App\Models\IncidentResponse;
use App\Models\SecurityAlert;
use App\Models\User;
use Illuminate\Http\Request;

class IncidentResponseController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin');
    }

    /**
     * Show incidents dashboard
     */
    public function dashboard()
    {
        $data = [
            'total_incidents' => IncidentCase::count(),
            'active_incidents' => IncidentCase::where('status', '!=', 'closed')->count(),
            'critical_incidents' => IncidentCase::where('severity', 'critical')->count(),
            'incidents_this_week' => IncidentCase::where('created_at', '>=', now()->subWeek())->count(),
            'average_resolution_time' => $this->getAverageResolutionTime(),
            'recent_incidents' => IncidentCase::orderBy('created_at', 'desc')->limit(10)->get(),
            'incidents_by_severity' => $this->getIncidentsBySeverity(),
        ];

        return view('admin.incidents.dashboard', $data);
    }

    /**
     * List all incidents
     */
    public function index(Request $request)
    {
        $query = IncidentCase::query();

        // Filters
        if ($request->get('status')) {
            $query->where('status', $request->get('status'));
        }

        if ($request->get('severity')) {
            $query->where('severity', $request->get('severity'));
        }

        if ($request->get('assignee')) {
            $query->where('assigned_to', $request->get('assignee'));
        }

        $incidents = $query->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.incidents.index', [
            'incidents' => $incidents,
            'statuses' => ['open', 'investigating', 'contained', 'resolved', 'closed'],
            'severities' => ['low', 'medium', 'high', 'critical'],
            'team_members' => User::where('role', 'admin')->get(),
        ]);
    }

    /**
     * Show incident details
     */
    public function show($id)
    {
        $incident = IncidentCase::with('alertResponse', 'assignee', 'responses')->findOrFail($id);

        return view('admin.incidents.show', [
            'incident' => $incident,
            'timeline' => $this->getIncidentTimeline($incident),
            'related_alerts' => $incident->alertResponse?->alert->with('user')->get() ?? collect(),
            'team_members' => User::where('role', 'admin')->get(),
        ]);
    }

    /**
     * Create incident
     */
    public function create(Request $request)
    {
        $alertId = $request->get('alert_id');

        return view('admin.incidents.create', [
            'alert' => $alertId ? SecurityAlert::find($alertId) : null,
            'users' => User::get(),
        ]);
    }

    /**
     * Store incident
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'severity' => 'required|in:low,medium,high,critical',
            'incident_type' => 'required|string',
            'affected_users' => 'required|array|min:1',
            'assigned_to' => 'nullable|exists:users,id',
            'related_alert_id' => 'nullable|exists:security_alerts,id',
        ]);

        $incident = IncidentCase::create([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'severity' => $validated['severity'],
            'incident_type' => $validated['incident_type'],
            'affected_users' => $validated['affected_users'],
            'assigned_to' => $validated['assigned_to'],
            'status' => 'open',
            'created_by' => auth()->id(),
            'related_alert_id' => $validated['related_alert_id'],
        ]);

        return redirect()->route('admin.incidents.show', $incident->id)
            ->with('success', 'Incident created successfully');
    }

    /**
     * Update incident status
     */
    public function updateStatus(Request $request, $id)
    {
        $incident = IncidentCase::findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|in:open,investigating,contained,resolved,closed',
            'notes' => 'nullable|string',
        ]);

        $incident->update([
            'status' => $validated['status'],
        ]);

        if ($validated['status'] === 'closed') {
            $incident->update(['closed_at' => now()]);
        }

        // Log response
        if ($validated['notes']) {
            IncidentResponse::create([
                'incident_case_id' => $incident->id,
                'responder_id' => auth()->id(),
                'action' => $validated['status'],
                'notes' => $validated['notes'],
            ]);
        }

        return back()->with('success', 'Incident status updated');
    }

    /**
     * Assign incident
     */
    public function assign(Request $request, $id)
    {
        $incident = IncidentCase::findOrFail($id);

        $validated = $request->validate([
            'assigned_to' => 'required|exists:users,id',
        ]);

        $incident->update($validated);

        return back()->with('success', 'Incident assigned');
    }

    /**
     * Add response to incident
     */
    public function addResponse(Request $request, $id)
    {
        $incident = IncidentCase::findOrFail($id);

        $validated = $request->validate([
            'action' => 'required|string',
            'notes' => 'required|string',
        ]);

        IncidentResponse::create([
            'incident_case_id' => $incident->id,
            'responder_id' => auth()->id(),
            'action' => $validated['action'],
            'notes' => $validated['notes'],
        ]);

        return back()->with('success', 'Response added');
    }

    /**
     * Close incident
     */
    public function close(Request $request, $id)
    {
        $incident = IncidentCase::findOrFail($id);

        $validated = $request->validate([
            'resolution' => 'required|string',
            'lessons_learned' => 'nullable|string',
        ]);

        $incident->update([
            'status' => 'closed',
            'closed_at' => now(),
            'resolution' => $validated['resolution'],
            'lessons_learned' => $validated['lessons_learned'],
        ]);

        return redirect()->route('admin.incidents.show', $incident->id)
            ->with('success', 'Incident closed');
    }

    /**
     * Generate incident report
     */
    public function report(Request $request, $id)
    {
        $incident = IncidentCase::findOrFail($id);

        $report = [
            'incident' => $incident,
            'timeline' => $this->getIncidentTimeline($incident),
            'summary' => $this->generateIncidentSummary($incident),
            'recommendations' => $this->getRecommendations($incident),
            'generated_at' => now(),
        ];

        if ($request->get('format') === 'pdf') {
            return $this->generatePdfReport($report);
        }

        return view('admin.incidents.report', $report);
    }

    /**
     * List incident response actions
     */
    public function responses($id)
    {
        $incident = IncidentCase::findOrFail($id);
        $responses = IncidentResponse::where('incident_case_id', $id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.incidents.responses', [
            'incident' => $incident,
            'responses' => $responses,
        ]);
    }

    /**
     * Get average resolution time
     */
    private function getAverageResolutionTime()
    {
        $closedIncidents = IncidentCase::where('status', 'closed')
            ->where('closed_at', '!=', null)
            ->get();

        if ($closedIncidents->isEmpty()) {
            return 0;
        }

        $totalTime = $closedIncidents->reduce(function ($sum, $incident) {
            return $sum + $incident->created_at->diffInHours($incident->closed_at);
        }, 0);

        return round($totalTime / $closedIncidents->count(), 1);
    }

    /**
     * Get incidents by severity
     */
    private function getIncidentsBySeverity()
    {
        return IncidentCase::selectRaw('severity, count(*) as count')
            ->groupBy('severity')
            ->get()
            ->pluck('count', 'severity');
    }

    /**
     * Get incident timeline
     */
    private function getIncidentTimeline($incident)
    {
        $timeline = [
            [
                'timestamp' => $incident->created_at,
                'action' => 'Incident Created',
                'details' => $incident->title,
                'by' => $incident->creator?->name ?? 'System',
            ],
        ];

        foreach (IncidentResponse::where('incident_case_id', $incident->id)->get() as $response) {
            $timeline[] = [
                'timestamp' => $response->created_at,
                'action' => $response->action,
                'details' => $response->notes,
                'by' => $response->responder->name,
            ];
        }

        return collect($timeline)->sortBy('timestamp')->values();
    }

    /**
     * Generate incident summary
     */
    private function generateIncidentSummary($incident)
    {
        return [
            'total_affected_users' => count($incident->affected_users),
            'total_responses' => IncidentResponse::where('incident_case_id', $incident->id)->count(),
            'time_to_resolution' => $incident->closed_at
                ? $incident->created_at->diffInHours($incident->closed_at)
                : null,
            'severity_level' => strtoupper($incident->severity),
        ];
    }

    /**
     * Get recommendations
     */
    private function getRecommendations($incident)
    {
        $recommendations = [];

        if ($incident->severity === 'critical') {
            $recommendations[] = 'Conduct full security audit';
            $recommendations[] = 'Review and update security policies';
            $recommendations[] = 'Notify affected users';
        }

        if ($incident->incident_type === 'unauthorized_access') {
            $recommendations[] = 'Force password reset for affected users';
            $recommendations[] = 'Review access logs';
            $recommendations[] = 'Implement additional authentication controls';
        }

        if ($incident->incident_type === 'data_breach') {
            $recommendations[] = 'Notify relevant authorities if required';
            $recommendations[] = 'Offer credit monitoring to affected users';
            $recommendations[] = 'Enhance data encryption';
        }

        return $recommendations;
    }

    /**
     * Generate PDF report
     */
    private function generatePdfReport($report)
    {
        // PDF generation logic
        return view('admin.incidents.report-pdf', $report);
    }
}
