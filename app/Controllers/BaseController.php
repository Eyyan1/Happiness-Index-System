<?php namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
use App\Models\NotificationModel;

abstract class BaseController extends Controller
{
    /**
     * Instance of the main Request object.
     *
     * @var CLIRequest|IncomingRequest
     */
    protected $request;

    /**
     * An array of helpers to be loaded automatically upon
     * class instantiation.
     *
     * @var list<string>
     */
    protected $helpers = [];

    /**
     * Unread notifications for the current user.
     *
     * @var array
     */
    protected $notifications = [];

    /**
     * Be sure to declare properties for any property fetch you initialized.
     * The creation of dynamic property is deprecated in PHP 8.2.
     */

    /**
     * Initialization and setup for all controllers.
     */
     public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);

        // If logged in, load unread notifications
        if (session()->get('isLoggedIn')) {
            $notifModel = new NotificationModel();
            $notes = $notifModel
                ->where('USER_ID', session('userId'))
                ->where('IS_READ', 'N')
                ->orderBy('DATE_CREATED', 'DESC')
                ->findAll();

            // Convert any OCILob MESSAGE to string
            foreach ($notes as &$n) {
                if (is_object($n['MESSAGE']) && method_exists($n['MESSAGE'], 'read')) {
                    $len = $n['MESSAGE']->size();
                    $n['MESSAGE'] = $len > 0
                        ? $n['MESSAGE']->read($len)
                        : '';
                }
            }
            unset($n);

            $this->notifications = $notes;
        }

        // Make available to all views:
        view()->setData('notifications', $this->notifications);
    }
}
