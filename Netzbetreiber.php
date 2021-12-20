<?php

namespace Mocca;

use Slim\Http\Request;
use Slim\Http\Response;
use Interop\Container\ContainerInterface;
use \PDO;


class Netzbetreiber
{
    public function __construct($db = 0)
    {
        global $app;
        $this->container = $app->getContainer();
        $this->db = $this->container['db'];
    }

    public function setValue($name,$value){
        $this->data->$name = $value;
    }

    public function getNetworkOperators() {    
        $statement = $this->db->prepare("SELECT * FROM sms_netzbetreiber 
        LEFT JOIN sms_filiale_netzbetreiber ON filnetz_netz = netz_id 
        AND filnetz_filiale = :filnetz_filiale  GROUP BY netz_id ORDER BY netz_name");
        $dataArrayFirst = array('filnetz_filiale' => $this->data->filnetz_filiale);

        if ($statement->execute($dataArrayFirst)) {
            $data["status"] = 'success';
            $data['data'] = array();

            while ($row = $statement->fetch()) {
                $tmp['netz_id'] = $row['netz_id'];
                $tmp['netz_name'] = $row['netz_name'];
                $tmp['netz_vvl'] = $row['netz_vvl'];
                $tmp['netz_kurz'] = $row['netz_kurz'];
                $tmp['netz_vo'] = $row['netz_vo'];
                $tmp['netz_bemerkung'] = $row['netz_bemerkung'];
                $tmp['netz_nomitmarge'] = (bool) $row['netz_nomitmarge']; // without boolean it is not stable.
                $tmp['netz_vertragsdauer'] = $row['netz_vertragsdauer'];
                if($row['filnetz_id'] > 0){
                    $tmp['active'] = 1;
                } else {
                    $tmp['active'] = 0;
                }
                
                $result2 = $this->db->prepare("SELECT COUNT(*) as count 
                FROM sms_tarife WHERE tarif_netz_id='".$row['netz_id']."' ");

                if ($result2->execute()) {
                    while ($row2 = $result2->fetch()) {
                    $tmp['bestehende_tarife'] = $row2['count'];
                    foreach ($tmp as $key => $value) {
                        $tmp[$key] = $value;
                    }
                    $items[] = $tmp;
                }
                     
                }
                else {
                    $data['status'] = 'result array is not working!';
                }                  
            }
            $data['rowCount'] = $statement->rowCount();
            if (!empty($items)) {
                $data['data'] = $items;
            }

            else {
                $data['status'] = 'array is empty!';
            }
            
        }
        else {
            $data['status'] = 'failure';
        }
        
        return $data;
    }

    public function getNetworkOperatorNames() {    
        $statement = $this->db->prepare("SELECT * FROM sms_netzbetreiber 
        LEFT JOIN sms_filiale_netzbetreiber ON filnetz_netz = netz_id 
        AND filnetz_filiale = :filnetz_filiale  GROUP BY netz_id ORDER BY netz_name");
        $dataArrayFirst = array('filnetz_filiale' => $this->data->filnetz_filiale);

        if ($statement->execute($dataArrayFirst)) {
            $data["status"] = 'success';
            $data['data'] = array();

            while ($row = $statement->fetch()) {
                $tmp['netz_name'] = $row['netz_name'];    
                $items[] = $tmp;             
            }
            $data['rowCount'] = $statement->rowCount();
            if (!empty($items)) {
                $data['data'] = $items;
            }

            else {
                $data['status'] = 'array is empty!';
            }
            
        }
        else {
            $data['status'] = 'failure';
        }
        
        return $data;
    }

    public function getNetworkOperatorById($id) {
        $statement = $this->db->prepare("SELECT * FROM sms_netzbetreiber WHERE netz_id = :id");
        $dataArray = array('id' => $id);

        if($statement->execute($dataArray)) {
            $data = $statement->fetch();
        }     
        else {
            $data["status"] = 'failure';
        }
        return $data;
    }

    public function createNetworkOperator() {
        $statement = $this->db->prepare("INSERT INTO sms_netzbetreiber SET 
        netz_name = :netz_name
        -- netz_vvl = :netz_vvl, 
        -- netz_bild = :netz_bild, 
        -- netz_vvl_period = :netz_vvl_period, 
        -- netz_vertragsdauer = :netz_vertragsdauer,
        -- netz_bemerkung = :netz_bemerkung,
        -- netz_kurz = :netz_kurz,
        -- netz_vo = :netz_vo,
        -- netz_filiale = :netz_filiale,
        -- netz_nomitmarge = :netz_nomitmarge
        ");
        $dataArray = array(
            'netz_name' => $this->data->netz_name
            // 'netz_vvl' => $this->data->netz_vvl,
            // 'netz_bild' => $this->data->netz_bild, 
            // 'netz_vvl_period' => $this->data->netz_vvl_period,
            // 'netz_vertragsdauer' => $this->data->netz_vertragsdauer,  
            // 'netz_bemerkung' => $this->data->netz_bemerkung,
            // 'netz_kurz' => $this->data->netz_kurz, 
            // 'netz_vo' => $this->data->netz_vo,
            // 'netz_filiale' => $this->data->netz_filiale, 
            // 'netz_nomitmarge' => $this->data->netz_nomitmarge
        );

        if($statement->execute($dataArray)) {
            $data["status"] = 'success';
        }     
        else {
            $data["status"] = 'failure';
        }
        return $data;
    }
    
    public function updateNetworkOperator() {

        if($this->data->something == null) {

            $statement = $this->db->prepare("UPDATE sms_netzbetreiber SET
            netz_name = :netz_name,
            netz_vvl = :netz_vvl, 
            -- netz_bild = :netz_bild, 
            -- netz_vvl_period = :netz_vvl_period, 
            netz_vertragsdauer = :netz_vertragsdauer,
            netz_bemerkung = :netz_bemerkung,
            netz_kurz = :netz_kurz,
            netz_vo = :netz_vo,
            -- netz_filiale = :netz_filiale,
            netz_nomitmarge = :netz_nomitmarge
            WHERE netz_id = :id");
            
            $dataArray = array(
                'id' => $this->data->id,
                'netz_name' => $this->data->netz_name,
                'netz_vvl' => $this->data->netz_vvl,
                // 'netz_bild' => $this->data->netz_bild, 
                // 'netz_vvl_period' => $this->data->netz_vvl_period,
                'netz_vertragsdauer' => $this->data->netz_vertragsdauer,  
                'netz_bemerkung' => $this->data->netz_bemerkung,
                'netz_kurz' => $this->data->netz_kurz, 
                'netz_vo' => $this->data->netz_vo,
                // 'netz_filiale' => $this->data->netz_filiale, 
                'netz_nomitmarge' => $this->data->netz_nomitmarge
            );
            
            if($statement->execute($dataArray)) {
                
                $data["status"] = 'success';
            }     
            else {
                $data["status"] = 'failure';
            }
    
            return $data;
        }
        
        if($this->data->something == 1) {

            $statement = $this->db->prepare("UPDATE sms_netzbetreiber
            LEFT JOIN sms_tarife ON sms_netzbetreiber.netz_id = sms_tarife.tarif_netz_id 
            SET 
            netz_name = :netz_name,
            netz_vvl = :netz_vvl,
            netz_vertragsdauer = :netz_vertragsdauer,
            netz_bemerkung = :netz_bemerkung,
            netz_kurz = :netz_kurz,
            netz_vo = :netz_vo,
            netz_nomitmarge = :netz_nomitmarge,
            tarif_vvl = :netz_vvl,
            -- tarif_vvl_period = 'M',
            tarif_vertragsdauer = :netz_vertragsdauer 
            WHERE netz_id = :id");
            $dataArray = array(
                'id' => $this->data->id,
                'netz_name' => $this->data->netz_name,
                'netz_vvl' => $this->data->netz_vvl,
                // 'tarif_vvl_period' => $this->data->tarif_vvl_period,  
                'netz_vertragsdauer' => $this->data->netz_vertragsdauer,
                'netz_bemerkung' => $this->data->netz_bemerkung,
                'netz_kurz' => $this->data->netz_kurz, 
                'netz_vo' => $this->data->netz_vo,
                'netz_nomitmarge' => $this->data->netz_nomitmarge
            );
        
            if($statement->execute($dataArray)) {
                $data["status"] = 'success';
            }     
            else {
                $data["status"] = 'failure';
            }
            return $data;

        }
        
        // it is working.
        if($this->data->something == 2) {
            $statement = $this->db->prepare("UPDATE sms_tarife SET 
            tarif_vvl = :netz_vvl,
            tarif_vertragsdauer = :netz_vertragsdauer
            WHERE tarif_netz_id = :id");
            $dataArray = array(
                'id' => $this->data->id,
                'netz_vvl' => $this->data->netz_vvl,
                'netz_vertragsdauer' => $this->data->netz_vertragsdauer
            );
            if($statement->execute($dataArray)) {
                $data["status"] = 'success';
            }     
            else {
                $data["status"] = 'failure';
            }

            $statement = $this->db->prepare("UPDATE sms_vertragslog 
            LEFT JOIN sms_tarife ON vertrag_tarif = tarif_id 
            LEFT JOIN sms_netzbetreiber ON sms_netzbetreiber.netz_id = sms_tarife.tarif_netz_id
            SET vertrag_ende=date_add(vertragsbeginn,INTERVAL :netz_vertragsdauer MONTH), 
            vertrag_vvl=date_add(vertragsbeginn,INTERVAL 
            :netz_vertragsdauer-:netz_vvl MONTH),
            tarif_vvl = :netz_vvl,
            tarif_vertragsdauer = :netz_vertragsdauer,
            netz_name = :netz_name,
            netz_vvl = :netz_vvl,
            netz_vertragsdauer = :netz_vertragsdauer,
            netz_bemerkung = :netz_bemerkung,
            netz_kurz = :netz_kurz,
            netz_vo = :netz_vo,
            netz_nomitmarge = :netz_nomitmarge
            WHERE tarif_netz_id = :id
            AND vertrag_geloescht <> 'geloescht'");
            $dataArray = array(
                'id' => $this->data->id,
                'netz_name' => $this->data->netz_name,
                'netz_vvl' => $this->data->netz_vvl,
                // 'tarif_vvl_period' => $this->data->tarif_vvl_period,  
                'netz_vertragsdauer' => $this->data->netz_vertragsdauer,
                'netz_bemerkung' => $this->data->netz_bemerkung,
                'netz_kurz' => $this->data->netz_kurz, 
                'netz_vo' => $this->data->netz_vo,
                'netz_nomitmarge' => $this->data->netz_nomitmarge
            );

            if($statement->execute($dataArray)) {
                $data["status"] = 'success';
            }     
            else {
                $data["status"] = 'failure';
            }
            return $data;

        }
        
    }

    public function deleteNetworkOperatorById($id) {
        $statement = $this->db->prepare("DELETE FROM sms_netzbetreiber WHERE netz_id = :id");
        $dataArray = array('id' => $id);

        if($statement->execute($dataArray)) {
            $data["status"] = 'success';
        }     
        else {
            $data["status"] = 'failure';
        }
        return $data;
    }

    public function activatedNetworkOperator() {
        $statement = $this->db->prepare("INSERT INTO sms_filiale_netzbetreiber SET 
        filnetz_filiale = :filnetz_filiale, 
        filnetz_netz = :netz_id");
        $dataArray = array(
            'netz_id' => $this->data->netz_id,
            'filnetz_filiale' => $this->data->filnetz_filiale
        );

        if($statement->execute($dataArray)) {
            $data["status"] = 'success';
        }     
        else {
            $data["status"] = 'failure';
        }
        return $data;
    }

    public function inActivatedNetworkOperator() {
        $statement = $this->db->prepare("DELETE FROM sms_filiale_netzbetreiber WHERE filnetz_filiale = :filiale_id AND filnetz_netz = :netz_id");
        $dataArray = array('filiale_id' => $this->data->filiale_id ,'netz_id' => $this->data->netz_id);

        if($statement->execute($dataArray)) {
            $data["status"] = 'success';
        }     
        else {
            $data["status"] = 'failure';
        }
        return $data;
    }

    // MIN
    public function minDate(){
        $statement = $this->db->prepare("SELECT MIN(vertrag_erfassung) AS min
        From sms_vertragslog 
        LEFT JOIN sms_tarife ON sms_tarife.tarif_id = sms_vertragslog.vertrag_tarif
        LEFT JOIN sms_netzbetreiber ON sms_netzbetreiber.netz_id = sms_tarife.tarif_netz_id
        -- WHERE vertrag_filiale IN (2)
        ");
        $statement->execute();
        $row = $statement->fetch();
        $min = $row['min'];
        return $min;
    }

    // MAX
    public function maxDate(){
        $statement = $this->db->prepare("SELECT MAX(vertrag_erfassung) AS max
        From sms_vertragslog 
        LEFT JOIN sms_tarife ON sms_tarife.tarif_id = sms_vertragslog.vertrag_tarif
        LEFT JOIN sms_netzbetreiber ON sms_netzbetreiber.netz_id = sms_tarife.tarif_netz_id
        -- WHERE vertrag_filiale IN (2)
        ");
        $statement->execute();
        $row = $statement->fetch();
        $max = $row['max'];
        return $max;
    }

    public function getNetworkOperatorContracts() {
            for($i=0; $i < count($this->data->test); $i++) {
            $full .= $this->data->test[$i];
            if ($this->data->test[$i] != $this->data->test[count($this->data->test)-1] ) {
            $full .= ", ";
            }
        }
        $statement = $this->db->prepare("SELECT netz_name, COUNT(netz_id) AS count
         FROM sms_vertragslog 
         LEFT JOIN sms_tarife ON sms_tarife.tarif_id = sms_vertragslog.vertrag_tarif
         LEFT JOIN sms_netzbetreiber ON sms_netzbetreiber.netz_id = sms_tarife.tarif_netz_id
         WHERE netz_id AND vertrag_erfassung -- IN (:test)
         BETWEEN :vertrag_erfassung_start AND :vertrag_erfassung_end AND vertrag_filiale IN (:vertrag_filiale)
         GROUP BY netz_name");
        $dataArrayFirst = array(
                    // 'vertrag_erfassung_start' => $min ?? $this->minDate(),
                    // 'vertrag_erfassung_end' => $max ?? $this->maxDate(),
                    // 'test' => $this->data->test,
                    'vertrag_erfassung_start' => $this->data->vertrag_erfassung_start,
                    'vertrag_erfassung_end' => $this->data->vertrag_erfassung_end,
                    'vertrag_filiale' => $this->data->vertrag_filiale
        );

            if($statement->execute($dataArray)) {
                $data['status'] = 'success';
                $data['data'] = $statement->fetchAll();
                $data['rowCount'] = $statement->rowCount();
            }
            else {
                $data['status'] = 'failure';
            }
        return $data;
    }

    // per date range
    public function getNetworkOperatorContractsPerDateRange() {    
        $statement = $this->db->prepare("SELECT vertrag_id,
        vertragsbeginn,
        TIMESTAMP,
        vertrag_ende,
        vertrag_vvl,
        vertrag_tarif,
        vertrag_filiale,
        vertrag_erfassung,
        tarif_id,
        tarif_netz_id,
        tarif_name,
        tarif_vvl,
        tarif_vertragsdauer,
        netz_id,
        netz_name,
        netz_vvl,
        netz_vertragsdauer
        From sms_vertragslog 
        LEFT JOIN sms_tarife ON sms_tarife.tarif_id = sms_vertragslog.vertrag_tarif
        LEFT JOIN sms_netzbetreiber ON sms_netzbetreiber.netz_id = sms_tarife.tarif_netz_id
        WHERE netz_id = :id AND vertrag_erfassung
        BETWEEN :vertrag_erfassung_start AND :vertrag_erfassung_end AND vertrag_filiale IN (:vertrag_filiale)
        ORDER BY vertrag_erfassung");
        
        $dataArrayFirst = array(
            'id' => $this->data->id,
            'vertrag_erfassung_start' => $this->data->vertrag_erfassung_start,
            'vertrag_erfassung_end' => $this->data->vertrag_erfassung_end,
            'vertrag_filiale' => $this->data->vertrag_filiale
        );

        if ($statement->execute($dataArrayFirst)) {
            $data["status"] = 'success';
            $data['data'] = array();

            while ($row = $statement->fetch()) {

                $tmp['netz_id'] = $row['netz_id'];
                $tmp['netz_name'] = $row['netz_name'];
                $tmp['vertrag_erfassung'] = $row['vertrag_erfassung'];
                $tmp['netz_vvl'] = $row['netz_vvl'];
                $tmp['vertrag_erfassung_start'] = $this->data->vertrag_erfassung_start;
                $tmp['vertrag_erfassung_end'] = $this->data->vertrag_erfassung_end;
                
                $result2 = $this->db->prepare("SELECT COUNT(*) as count
                From sms_vertragslog 
                LEFT JOIN sms_tarife ON sms_tarife.tarif_id = sms_vertragslog.vertrag_tarif
                LEFT JOIN sms_netzbetreiber ON sms_netzbetreiber.netz_id = sms_tarife.tarif_netz_id
                WHERE netz_id = '".$row['netz_id']."' AND vertrag_erfassung
                BETWEEN '".$tmp['vertrag_erfassung_start']."' AND '".$tmp['vertrag_erfassung_end']."' 
                AND vertrag_filiale IN ('".$row['vertrag_filiale']."')
                ORDER BY vertrag_erfassung");

                if ($result2->execute()) {
                    while ($row2 = $result2->fetch()) {
                    $tmp['vertrag_count'] = $row2['count'];
                    foreach ($tmp as $key => $value) {
                        $tmp[$key] = $value;
                    }
                    $items[] = $tmp;
                }
                     
                }
                else {
                    $data['status'] = 'result array is not working!';
                }                  
            }
            $data['rowCount'] = $statement->rowCount();
            if (!empty($items)) {
                $data['data'] = $items;
            }

            else {
                $data['status'] = 'array is empty!';
            }
            
        }
        else {
            $data['status'] = 'failure';
        }
        
        return $data;
    }

    public function getNetworkOperatorStores() {
        $statement = $this->db->prepare("SELECT id,name FROM sms_config WHERE multifiliale = :multifiliale");
        $dataArray = array('multifiliale' => $this->data->multifiliale);
        if($statement->execute($dataArray)) {
            $data['status'] = 'success';
            $data['data'] = $statement->fetchAll();
            $data['rowCount'] = $statement->rowCount();
        }
        else {
            $data['status'] = 'failure';
        }
        return $data;
    }

}
