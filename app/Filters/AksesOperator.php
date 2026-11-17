<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

final class AksesOperator implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null): ?ResponseInterface
    {
        if (session()->has('operator_id')) {
            return null;
        }

        return redirect()->route('masuk', [], $request->getMethod() === 'GET' ? 302 : 303)
            ->setHeader('Cache-Control', 'private, no-store');
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null): ResponseInterface
    {
        return $response->setHeader('Cache-Control', 'private, no-store');
    }
}
