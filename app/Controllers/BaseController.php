<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * IL "PADRE" DI TUTTI I CONTROLLER
 *
 * Ogni controller fa "extends BaseController", quindi eredita
 * queste piccole funzioni di comodo e non le deve riscrivere.
 */
abstract class BaseController extends Controller
{
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Attenzione: non togliere questa riga
        parent::initController($request, $response, $logger);
    }

    /**
     * L'id dell'utente loggato (preso da Shield).
     */
    protected function userId(): int
    {
        return (int) auth()->id();
    }

    /**
     * Torna alla pagina precedente con gli errori e i dati già scritti nel form.
     * Nella vista gli errori li leggi con: session('errori')
     */
    protected function indietroConErrori(array $errori): RedirectResponse
    {
        return redirect()->back()->withInput()->with('errori', $errori);
    }

    /**
     * Mostra la pagina 404 (es. workout che non esiste o non è tuo).
     */
    protected function nonTrovato(): never
    {
        throw PageNotFoundException::forPageNotFound();
    }
}
