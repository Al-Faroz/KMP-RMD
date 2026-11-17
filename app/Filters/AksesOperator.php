<?php

namespace App\Filters;

use App\Models\OperatorModel;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

final class AksesOperator implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null): ?ResponseInterface
    {
        $sesi = session();
        $id = $sesi->get('operator_id');
        if ($id !== null) {
            $model = model(OperatorModel::class);
            $operator = $model->find($id);
            if ($operator !== null) {
                $profil = $model->profilSesi($operator);
                if (hash_equals($profil['operator_kunci'], (string) $sesi->get('operator_kunci'))) {
                    $sesi->set('operator_nama', $profil['operator_nama']);

                    return null;
                }
            }

            $sesi->remove(['operator_id', 'operator_nama', 'operator_kunci']);
        }

        return redirect()->route('masuk', [], $request->getMethod() === 'GET' ? 302 : 303)
            ->setHeader('Cache-Control', 'private, no-store');
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null): ResponseInterface
    {
        return $response->setHeader('Cache-Control', 'private, no-store');
    }
}
