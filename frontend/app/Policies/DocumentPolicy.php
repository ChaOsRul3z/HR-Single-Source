<?php

namespace App\Policies;

use App\Models\Document;
use App\Models\User;

class DocumentPolicy
{
    /**
     * Determine whether the user can view any documents.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the document.
     */
    public function view(User $user, Document $document): bool
    {
        if ($document->is_restricted) {
            return $user->canAccessRestrictedDocuments();
        }

        return true;
    }

    /**
     * Determine whether the user can create documents.
     */
    public function create(User $user): bool
    {
        return $user->isHr();
    }

    /**
     * Determine whether the user can update the document.
     */
    public function update(User $user, Document $document): bool
    {
        return $user->isHr();
    }

    /**
     * Determine whether the user can delete the document.
     */
    public function delete(User $user, Document $document): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can archive or restore the document.
     */
    public function manageStatus(User $user, Document $document): bool
    {
        return $user->isHr();
    }

    /**
     * Determine whether the user can download the document's file.
     */
    public function download(User $user, Document $document): bool
    {
        return $this->view($user, $document);
    }
}
