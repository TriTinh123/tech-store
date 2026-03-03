<?php

namespace App\Http\Controllers;

use App\Models\EmailTemplate;
use Illuminate\Http\Request;

class EmailTemplateController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin');
    }

    /**
     * Show email templates list
     */
    public function index()
    {
        $templates = EmailTemplate::orderBy('template_type')
            ->orderBy('name')
            ->paginate(20);

        return view('admin.email-templates.index', [
            'templates' => $templates,
        ]);
    }

    /**
     * Create new template form
     */
    public function create()
    {
        return view('admin.email-templates.create');
    }

    /**
     * Store new template
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|unique:email_templates,slug',
            'subject' => 'required|string|max:255',
            'html_body' => 'required|string',
            'text_body' => 'nullable|string',
            'template_type' => 'required|string',
            'is_active' => 'boolean',
        ]);

        EmailTemplate::create($validated);

        return redirect()->route('admin.email-templates.index')
            ->with('success', 'Email template created successfully');
    }

    /**
     * Show template details
     */
    public function show($id)
    {
        $template = EmailTemplate::findOrFail($id);

        return view('admin.email-templates.show', [
            'template' => $template,
        ]);
    }

    /**
     * Edit template
     */
    public function edit($id)
    {
        $template = EmailTemplate::findOrFail($id);

        return view('admin.email-templates.edit', [
            'template' => $template,
        ]);
    }

    /**
     * Update template
     */
    public function update(Request $request, $id)
    {
        $template = EmailTemplate::findOrFail($id);

        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'html_body' => 'required|string',
            'text_body' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $template->update($validated);

        return redirect()->route('admin.email-templates.show', $template->id)
            ->with('success', 'Template updated successfully');
    }

    /**
     * Preview template
     */
    public function preview(Request $request, $id)
    {
        $template = EmailTemplate::findOrFail($id);

        $variables = $request->get('variables', []);
        $rendered = $template->render($variables);

        return view('admin.email-templates.preview', [
            'template' => $template,
            'rendered' => $rendered,
            'variables' => $variables,
        ]);
    }

    /**
     * Test send template
     */
    public function testSend(Request $request, $id)
    {
        $template = EmailTemplate::findOrFail($id);

        $validated = $request->validate([
            'email' => 'required|email',
            'variables' => 'nullable|array',
        ]);

        try {
            $rendered = $template->render($validated['variables'] ?? []);

            // Send email here
            \Mail::raw($rendered['body'], function ($message) use ($validated, $rendered) {
                $message->to($validated['email'])
                    ->subject($rendered['subject']);
            });

            return back()->with('success', 'Test email sent successfully');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to send test email: '.$e->getMessage());
        }
    }

    /**
     * List available templates
     */
    public function getTemplates()
    {
        return EmailTemplate::active()->get();
    }

    /**
     * Delete template
     */
    public function destroy($id)
    {
        $template = EmailTemplate::findOrFail($id);
        $template->delete();

        return redirect()->route('admin.email-templates.index')
            ->with('success', 'Email template deleted successfully');
    }
}
