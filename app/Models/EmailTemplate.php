<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailTemplate extends Model
{
    protected $table = 'email_templates';

    protected $fillable = [
        'name',
        'slug',
        'subject',
        'html_body',
        'text_body',
        'template_type',
        'is_active',
        'variables',
    ];

    protected $casts = [
        'variables' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Get active templates
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get by type
     */
    public function scopeByType($query, $type)
    {
        return $query->where('template_type', $type);
    }

    /**
     * Get by slug
     */
    public function scopeBySlug($query, $slug)
    {
        return $query->where('slug', $slug);
    }

    /**
     * Render template with variables
     */
    public function render($variables = [])
    {
        $subject = $this->subject;
        $body = $this->html_body;

        foreach ($variables as $key => $value) {
            $subject = str_replace("{{ $key }}", $value, $subject);
            $body = str_replace("{{ $key }}", $value, $body);
        }

        return [
            'subject' => $subject,
            'body' => $body,
        ];
    }
}
