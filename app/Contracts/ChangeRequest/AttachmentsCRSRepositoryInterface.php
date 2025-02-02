<?php

namespace App\Contracts\ChangeRequest;

interface AttachmentsCRSRepositoryInterface
{
    public function add_files($data,$id);
    public function update_files($data,$id);

}
