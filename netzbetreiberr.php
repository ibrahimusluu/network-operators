<?php


//se Ramsey\Uuid\Uuid;
use Firebase\JWT\JWT;
use Tuupola\Base62;
use Mocca\SearchCustomer;
use Mocca\SearchArticle;
use Mocca\SearchTarif;
use Mocca\Netzbetreiber;

// returns id,name for all records (this works nowhere in new Frontend)
$app->get("/netzbetreiber", function ($request, $response, $arguments) {
    $token = $request->getAttribute("jwt");
    $statement = $this->db->prepare("SELECT netz_id, netz_name FROM sms_filiale_netzbetreiber, sms_netzbetreiber 
                                        WHERE filnetz_filiale = :filiale AND filnetz_netz = netz_id
                                        Order By netz_name asc");
    $statement->execute(array('filiale' => $token['filiale']));
    while($row = $statement->fetch()){

        $tmp = array();
        $tmp['id'] = $row['netz_id'];
        $tmp['name'] = $row['netz_name'];
        $items[] = $tmp;

    }

    $data['status'] = 'success';
    $data['items'] = $items;
    $data['count'] = $statement->rowCount();

    return $response->withStatus(200)
        ->withHeader("Content-Type", "application/json")
        ->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
});

// returns all records
$app->get("/netzbetreiber/getAll", function ($request, $response, $arguments) {
    $token = $request->getAttribute("jwt");
    $network = new Netzbetreiber();

    $network->setValue('filnetz_filiale', $token['filiale']);

    $export = $network->getNetworkOperators();

    return $response->withStatus(200)
        ->withHeader("Content-Type", "application/json")
        ->write(json_encode($export, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
});

// returns Names
$app->get("/netzbetreiber/getAllNames", function ($request, $response, $arguments) {
    $token = $request->getAttribute("jwt");
    $network = new Netzbetreiber();

    $network->setValue('filnetz_filiale', $token['filiale']);

    $export = $network->getNetworkOperatorNames();

    return $response->withStatus(200)
        ->withHeader("Content-Type", "application/json")
        ->write(json_encode($export, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
});

// returns by id
$app->get("/netzbetreiber/{id}", function ($request, $response, $arguments) {
    $id = (int) $arguments['id'];
    $network = new Netzbetreiber();
    $export = $network->getNetworkOperatorById($id);

    return $response->withStatus(200)
        ->withHeader("Content-Type", "application/json")
        ->write(json_encode($export, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
});

// inserts a new record
$app->post("/netzbetreiber/createNetworkOperator", function ($request, $response, $arguments) {
    $json = $request->getBody();
    $data = json_decode($json,true); 

    $network = new Netzbetreiber();

    $network->setValue('netz_name', $data['netz_name']);
    // $network->setValue('netz_vvl', $data['netz_vvl']);
    // $network->setValue('netz_bild', $data['netz_bild']);
    // $network->setValue('netz_vvl_period', c);
    // $network->setValue('netz_vertragsdauer', $data['netz_vertragsdauer']);
    // $network->setValue('netz_bemerkung', $data['netz_bemerkung']);
    // $network->setValue('netz_kurz', $data['netz_kurz']);
    // $network->setValue('netz_vo', $data['netz_vo']);
    // $network->setValue('netz_filiale', $data['netz_filiale']);
    // $network->setValue('netz_nomitmarge', $data['netz_nomitmarge']);

    $export = $network->createNetworkOperator();

    return $response->withStatus(200)
        ->withHeader("Content-Type", "application/json")
        ->write(json_encode($export, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
});

// update an existing record
$app->post("/netzbetreiber/updateNetworkOperator", function ($request, $response, $arguments) {
    $json = $request->getBody();
    $data = json_decode($json,true);

    $network = new Netzbetreiber();

    $network->setValue('id', $data['id']);
    $network->setValue('something', $data['all']);
    $network->setValue('netz_name', $data['netz_name']);
    $network->setValue('netz_vvl', $data['netz_vvl']);
    // $network->setValue('netz_bild', $data['netz_bild']);
    // $network->setValue('netz_vvl_period', $data['netz_vertragsdauer']);
    $network->setValue('netz_vertragsdauer', $data['netz_vertragsdauer']);
    $network->setValue('netz_bemerkung', $data['netz_bemerkung']);
    $network->setValue('netz_kurz', $data['netz_kurz']);
    $network->setValue('netz_vo', $data['netz_vo']);
    // $network->setValue('netz_filiale', $data['netz_filiale']);
    $network->setValue('netz_nomitmarge', $data['netz_nomitmarge']);

    $export = $network->updateNetworkOperator();

    return $response->withStatus(200)
        ->withHeader("Content-Type", "application/json")
        ->write(json_encode($export, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

});

// delete an existing record
$app->delete("/netzbetreiber/deleteNetworkOperator/{id}", function ($request, $response, $arguments) {
    $id = (int) $arguments['id'];
    $network = new Netzbetreiber();
    $export = $network->deleteNetworkOperatorById($id); 

    return $response->withStatus(200)
        ->withHeader("Content-Type", "application/json")
        ->write(json_encode($export, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

});

// adding new filnetz_id with existing netz_id(filnetz_netz column)
$app->post("/netzbetreiber/activate", function ($request, $response, $arguments) {
    $token = $request->getAttribute("jwt");
    $json = $request->getBody();
    $data = json_decode($json,true);

    $network = new Netzbetreiber();

    $network->setValue('netz_id', $data['id']);
    $network->setValue('filnetz_filiale', $token['filiale']);

    $export = $network->activatedNetworkOperator();

    return $response->withStatus(200)
        ->withHeader("Content-Type", "application/json")
        ->write(json_encode($export, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
});

// deleting existing filnetz_netz together with existing filnetz_id
$app->post("/netzbetreiber/deactivate", function ($request, $response, $arguments) {
    $token = $request->getAttribute("jwt");
    $json = $request->getBody();
    $data = json_decode($json,true);

    $network = new Netzbetreiber();

    $network->setValue('netz_id', $data['id']);
    $network->setValue('filiale_id', $token['filiale']);

    $export = $network->inActivatedNetworkOperator();

    return $response->withStatus(200)
        ->withHeader("Content-Type", "application/json")
        ->write(json_encode($export, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
});

// returns all Contracts with Their counts
$app->post("/netzbetreiberr/getAllContracts", function ($request, $response, $arguments) {
    $token = $request->getAttribute("jwt");
    $json = $request->getBody();
    $data = json_decode($json,true);

    $network = new Netzbetreiber();

    $network->setValue('test', $data['testPostman']);
    // $network->setValue('id', $data['id']);
    $network->setValue('vertrag_erfassung_start', $data['vertrag_erfassung_start']);
    $network->setValue('vertrag_erfassung_end', $data['vertrag_erfassung_end']);
    $network->setValue('vertrag_filiale', $token['filiale']);

    $export = $network->getNetworkOperatorContracts();

    return $response->withStatus(200)
        ->withHeader("Content-Type", "application/json")
        ->write(json_encode($export, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
});

// returns Contracts per date range
$app->get("/netzbetreiberr/getAllContractsPerDataRange", function ($request, $response, $arguments) {
    $token = $request->getAttribute("jwt");
    $json = $request->getBody();
    $data = json_decode($json,true);

    $network = new Netzbetreiber();

    $network->setValue('id', $data['id']);
    // $network->setValue('vertrag_erfassung', $data['vertrag_erfassung']);
    $network->setValue('vertrag_erfassung_start', $data['vertrag_erfassung_start']);
    $network->setValue('vertrag_erfassung_end', $data['vertrag_erfassung_end']);
    $network->setValue('vertrag_filiale', $token['filiale']);

    $export = $network->getNetworkOperatorContractsPerDateRange();

    return $response->withStatus(200)
        ->withHeader("Content-Type", "application/json")
        ->write(json_encode($export, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
});

// returns Stores
$app->get("/netzbetreiberr/getAllStores", function ($request, $response, $arguments) {
    $network = new Netzbetreiber();
    $network->setValue('multifiliale', 1);
    $export = $network->getNetworkOperatorStores();

    return $response->withStatus(200)
        ->withHeader("Content-Type", "application/json")
        ->write(json_encode($export, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
});

?>