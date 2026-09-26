<?php

declare(strict_types=1);

namespace Enlivy\Enums\Helpdesk;

use Enlivy\Enums\Concern\EnumValues;

enum MessageContentTypes: string
{
    use EnumValues;

    case TEXT = 'text';
    case HTML = 'html';
    case IDENTITY_REQUEST = 'identity_request';
    case ASSIGNED = 'assigned';
    case UNASSIGNED = 'unassigned';
    case SNOOZED = 'snoozed';
    case UNSNOOZED = 'unsnoozed';
    case REOPENED = 'reopened';
    case RESOLVED = 'resolved';
    case CLOSED = 'closed';
    case PRIORITY_CHANGED = 'priority_changed';
    case PARTICIPANT_ADDED = 'participant_added';
    case PARTICIPANT_REMOVED = 'participant_removed';
    case MERGED = 'merged';
    case CONTINUED = 'continued';
    case RATING_REQUESTED = 'rating_requested';
    case RATED = 'rated';
    case INACTIVITY_REMINDER = 'inactivity_reminder';
    case AUTO_RESOLVED = 'auto_resolved';
    case AUTO_RESPONSE = 'auto_response';
    case TRANSCRIPT = 'transcript';
    case MARKED_SPAM = 'marked_spam';
    case MARKED_NOT_SPAM = 'marked_not_spam';
}
