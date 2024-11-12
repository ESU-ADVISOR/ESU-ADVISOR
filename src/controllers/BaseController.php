<?php
namespace Controllers;

interface BaseController
{
    public function handleGETRequest(array $get = []): void;

    public function handlePOSTRequest(array $post = []);
}
?>
