<?php

return [
    'userManagement' => [
        'title'          => 'User management',
        'title_singular' => 'User management',
    ],
    'permission' => [
        'title'          => 'Permissions',
        'title_singular' => 'Permission',
        'fields'         => [
            'id'                => 'ID',
            'id_helper'         => ' ',
            'title'             => 'Title',
            'title_helper'      => ' ',
            'created_at'        => 'Created at',
            'created_at_helper' => ' ',
            'updated_at'        => 'Updated at',
            'updated_at_helper' => ' ',
            'deleted_at'        => 'Deleted at',
            'deleted_at_helper' => ' ',
        ],
    ],
    'role' => [
        'title'          => 'Roles',
        'title_singular' => 'Role',
        'fields'         => [
            'id'                 => 'ID',
            'id_helper'          => ' ',
            'title'              => 'Title',
            'title_helper'       => ' ',
            'permissions'        => 'Permissions',
            'permissions_helper' => ' ',
            'created_at'         => 'Created at',
            'created_at_helper'  => ' ',
            'updated_at'         => 'Updated at',
            'updated_at_helper'  => ' ',
            'deleted_at'         => 'Deleted at',
            'deleted_at_helper'  => ' ',
        ],
    ],
    'user' => [
        'title'          => 'Users',
        'title_singular' => 'User',
        'fields'         => [
            'id'                           => 'ID',
            'id_helper'                    => ' ',
            'name'                         => 'Name',
            'name_helper'                  => ' ',
            'email'                        => 'Email',
            'email_helper'                 => ' ',
            'email_verified_at'            => 'Email verified at',
            'email_verified_at_helper'     => ' ',
            'password'                     => 'Password',
            'password_helper'              => ' ',
            'roles'                        => 'Roles',
            'roles_helper'                 => ' ',
            'remember_token'               => 'Remember Token',
            'remember_token_helper'        => ' ',
            'created_at'                   => 'Created at',
            'created_at_helper'            => ' ',
            'updated_at'                   => 'Updated at',
            'updated_at_helper'            => ' ',
            'deleted_at'                   => 'Deleted at',
            'deleted_at_helper'            => ' ',
            'verified'                     => 'Verified',
            'verified_helper'              => ' ',
            'verified_at'                  => 'Verified at',
            'verified_at_helper'           => ' ',
            'verification_token'           => 'Verification token',
            'verification_token_helper'    => ' ',
            'two_factor'                   => 'Two-Factor Auth',
            'two_factor_helper'            => ' ',
            'two_factor_code'              => 'Two-factor code',
            'two_factor_code_helper'       => ' ',
            'two_factor_expires_at'        => 'Two-factor expires at',
            'two_factor_expires_at_helper' => ' ',
        ],
    ],
    'session' => [
        'title'          => 'Session',
        'title_singular' => 'Session',
        'fields'         => [
            'id'                   => 'ID',
            'id_helper'            => ' ',
            'name'                 => 'Name',
            'name_helper'          => ' ',
            'audio'                => 'Audio',
            'audio_helper'         => ' ',
            'transcription'        => 'Transcription',
            'transcription_helper' => ' ',
            'summary'              => 'Summary',
            'summary_helper'       => ' ',
            'status'               => 'Status',
            'status_helper'        => ' ',
            'user'                 => 'User',
            'user_helper'          => ' ',
            'created_at'           => 'Created at',
            'created_at_helper'    => ' ',
            'updated_at'           => 'Updated at',
            'updated_at_helper'    => ' ',
            'deleted_at'           => 'Deleted at',
            'deleted_at_helper'    => ' ',
            'audio_url'            => 'Audio URL',
            'audio_url_helper'     => ' ',
            'task_created'         => 'Task Created',
            'task_created_helper'  => ' ',
            'total_tasks'          => 'Total Tasks',
            'total_tasks_helper'   => ' ',
            'tokens_used'          => 'Tokens Used',
            'tokens_used_helper'   => ' ',
            'recording'            => 'Recording',
        ],
    ],
    'todo' => [
        'title'          => 'Todo',
        'title_singular' => 'Todo',
        'fields'         => [
            'id'                   => 'ID',
            'id_helper'            => ' ',
            'item'                 => 'Item',
            'item_helper'          => ' ',
            'due_date'             => 'Due Date',
            'due_date_helper'      => ' ',
            'note'                 => 'Note',
            'note_helper'          => ' ',
            'session'              => 'Session',
            'session_helper'       => ' ',
            'created_at'           => 'Created at',
            'created_at_helper'    => ' ',
            'updated_at'           => 'Updated at',
            'updated_at_helper'    => ' ',
            'deleted_at'           => 'Deleted at',
            'deleted_at_helper'    => ' ',
            'assigned_to'          => 'Assigned To',
            'assigned_to_helper'   => ' ',
            'completed'            => 'Completed',
            'completed_helper'     => ' ',
            'time_due'             => 'Time Due',
            'time_due_helper'      => ' ',
            'send_reminder'        => 'Send Reminder',
            'send_reminder_helper' => ' ',
        ],
    ],
    'payment' => [
        'title'          => 'Payments',
        'title_singular' => 'Payment',
        'fields'         => [
            'id'                        => 'ID',
            'id_helper'                 => ' ',
            'stripe_transaction'        => 'Stripe Transaction',
            'stripe_transaction_helper' => ' ',
            'amount'                    => 'Amount',
            'amount_helper'             => ' ',
            'email'                     => 'Email',
            'email_helper'              => ' ',
            'created_at'                => 'Created at',
            'created_at_helper'         => ' ',
            'updated_at'                => 'Updated at',
            'updated_at_helper'         => ' ',
            'deleted_at'                => 'Deleted at',
            'deleted_at_helper'         => ' ',
            'credits'                   => 'Credit Value',
            'credits_helper'            => ' ',
        ],
    ],
    'credit' => [
        'title'          => 'Credits',
        'title_singular' => 'Credit',
        'fields'         => [
            'id'                => 'ID',
            'id_helper'         => ' ',
            'points'            => 'Points',
            'points_helper'     => ' ',
            'email'             => 'Email',
            'email_helper'      => ' ',
            'created_at'        => 'Created at',
            'created_at_helper' => ' ',
            'updated_at'        => 'Updated at',
            'updated_at_helper' => ' ',
            'deleted_at'        => 'Deleted at',
            'deleted_at_helper' => ' ',
        ],
    ],
    'userAlert' => [
        'title'          => 'User Alerts',
        'title_singular' => 'User Alert',
        'fields'         => [
            'id'                => 'ID',
            'id_helper'         => ' ',
            'alert_text'        => 'Alert Text',
            'alert_text_helper' => ' ',
            'alert_link'        => 'Alert Link',
            'alert_link_helper' => ' ',
            'user'              => 'Users',
            'user_helper'       => ' ',
            'created_at'        => 'Created at',
            'created_at_helper' => ' ',
            'updated_at'        => 'Updated at',
            'updated_at_helper' => ' ',
        ],
    ],

];
