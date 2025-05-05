<?php

namespace App\Controller;

use App\Repository\OrderRepository;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class BillController extends AbstractController
{
    #[Route('/order/{id}/bill', name: 'app_bill_invoice')]
    public function index($id, OrderRepository $orderRepository): Response
    {
        $order = $orderRepository->find($id);

        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');
        $dompdf = new Dompdf($pdfOptions);

        $html = $this->renderView('bill/index.html.twig', [
            'order' => $order,
        ]);

        $dompdf->loadHtml($html);
        $dompdf->render();
        $dompdf->stream('Bill-' . $order->getId() . '.pdf', [
            "Attachment" => false // don't download the bill auto
        ]);

        return new Response('', Response::HTTP_OK, [
            'Content-Type' => 'application/pdf',
        ]);
    }
}
