<?php

declare(strict_types=1);

namespace Mostbyte\Auth\Traits;

use Mostbyte\Auth\Models\User;

trait Tokens
{
    /**
     * @var string
     */
    private string $fake_token = "Bearer eyJhbGciOiJodHRwOi8vd3d3LnczLm9yZy8yMDAxLzA0L3htbGRzaWctbW9yZSNobWFjLXNoYTUxMiIsInR5cCI6IkpXVCJ9.eyJodHRwOi8vc2NoZW1hcy54bWxzb2FwLm9yZy93cy8yMDA1LzA1L2lkZW50aXR5L2NsYWltcy9uYW1laWRlbnRpZmllciI6ImIwMTE4MjZjLWQ1MzAtNDllMy04Mzc0LTRmNjkwNGM1MzYzMyIsImh0dHA6Ly9zY2hlbWFzLnhtbHNvYXAub3JnL3dzLzIwMDUvMDUvaWRlbnRpdHkvY2xhaW1zL25hbWUiOiJ0ZXN0dGVzdCIsImh0dHA6Ly9zY2hlbWFzLm1pY3Jvc29mdC5jb20vd3MvMjAwOC8wNi9pZGVudGl0eS9jbGFpbXMvcm9sZSI6IiIsImV4cCI6MTY2Njk1NDkyMX0.lZ2QnfMcjYOKL8T5C1DxoEua_iTJURywaonVMpkFMQJf556N10oeSJJ9W5bbwBhwnn_jTvrU7I-OU-5R7SZnlw";

    /**
     * @var string|null
     */
    private ?string $token = null;

    /**
     * @param string $token
     * @return User
     */
    public function setToken(string $token): User
    {
        $this->token = $token;
        return $this;
    }

    /**
     * @return string
     */
    public function getToken(): string
    {
        return $this->token ?: $this->fake_token;
    }
}