<?php

namespace AmyBoyd\HistoryBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class HistoryApiController extends Controller
{
    /**
     * @Route(
     *     "/history/document",
     *     name="history_get_document_history",
     *     options={"expose"=true},
     *     host="api.%base_host%"
     * )
     * @Method({"GET"})
     *
     * Expects two query string parameters: 'documentType' and 'documentId'.
     */
    public function getDocumentHistoryAction(Request $request)
    {
        $document = $this->get('doctrine_mongodb')
            ->getManager()
            ->getRepository($request->query->get('documentType'))
            ->find($request->query->get('documentId'));

        return new JsonResponse([
            'history' => $document->getHistoryEvents() ? $document->getHistoryEvents()->toArray() : [],
        ]);
    }
}
