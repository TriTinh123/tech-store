<?php
// This file is for IDE (PhpStorm, VS Code Intelephense, etc.) support
namespace PHPSTORM_META {
    \override(\App\Models\User::query(), type(0));
    \override(\App\Models\User::find(), type(0)|null);
    \override(\App\Models\Notification::query(), type(0));
    \override(\App\Models\Notification::find(),  type(0)|null);
}
