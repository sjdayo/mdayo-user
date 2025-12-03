<?php

namespace Mdayo\User\Models\Traits;

trait HasStatus
{
    public function markAsActive()
    {
        return $this->updateStatus('active');
    }

    public function markAsDeactivated()
    {
        return $this->updateStatus('deactivated');
    }

    public function markAsDeleted()
    {
        return $this->updateStatus('deleted');
    }

    protected function updateStatus(string $status)
    {
        $this->status = $status;
        return $this->save();
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isDeactivated(): bool
    {
        return $this->status === 'deactivated';
    }

    public function isDeleted(): bool
    {
        return $this->status === 'deleted';
    }
}
