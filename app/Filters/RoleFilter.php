<?php namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class RoleFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $userRole = session()->get('role');  // e.g. 'user' or 'admin'
        // $arguments will be [ 'user' ] or [ 'admin' ]
        if (! in_array($userRole, $arguments)) {
            return redirect()->to('/')->with('error', 'Access denied');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // no-op
    }
}
