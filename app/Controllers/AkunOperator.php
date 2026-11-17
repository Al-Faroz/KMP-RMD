<?php

namespace App\Controllers;

use App\Models\OperatorModel;
use CodeIgniter\HTTP\ResponseInterface;

final class AkunOperator extends BaseController
{
    protected $helpers = ['url', 'form'];

    public function masuk(): ResponseInterface
    {
        if (session()->has('operator_id')) {
            return redirect()->route('data-siswa')->setHeader('Cache-Control', 'private, no-store');
        }

        return $this->form();
    }

    public function autentikasi(): ResponseInterface
    {
        if (session()->has('operator_id')) {
            return $this->berhasil();
        }

        $username = $this->request->getPost('username');
        $username = is_string($username) ? trim($username) : '';
        $password = $this->request->getPost('password');
        $throttler = service('throttler');
        $batas = 'rmd_login_' . hash('sha256', $this->request->getIPAddress());

        if (! $throttler->check($batas, 8, 60)) {
            return $this->form($username, 'Terlalu banyak percobaan. Coba lagi sebentar.', 429)
                ->setHeader('Retry-After', (string) $throttler->getTokenTime());
        }

        $operator = is_string($password) ? model(OperatorModel::class)->autentikasi($username, $password) : null;
        if ($operator === null) {
            return $this->form($username, 'Username atau kata sandi salah.', 401);
        }

        $throttler->remove($batas);
        $session = session();
        $session->regenerate(true);
        $session->set(['operator_id' => $operator['id'], 'operator_nama' => $operator['nama_lengkap']]);

        return $this->berhasil();
    }

    public function keluar(): ResponseInterface
    {
        $session = session();
        $session->remove(['operator_id', 'operator_nama']);
        $session->destroy();

        return redirect()->route('masuk', [], 303)->withCookies()->setHeader('Cache-Control', 'private, no-store');
    }

    private function berhasil(): ResponseInterface
    {
        return redirect()->route('data-siswa', [], 303)->withCookies()->setHeader('Cache-Control', 'private, no-store');
    }

    private function form(string $username = '', string $galat = '', int $status = 200): ResponseInterface
    {
        $username = mb_substr($username, 0, 64);

        return $this->response->setStatusCode($status)->setHeader('Cache-Control', 'private, no-store')
            ->setBody(view('operator/masuk', compact('username', 'galat')));
    }
}
