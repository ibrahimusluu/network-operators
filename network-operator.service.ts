import { Injectable } from '@angular/core';
import { HttpClient } from "@angular/common/http";

@Injectable({
  providedIn: 'root'
})
export class NetworkOperatorService {

  constructor(private http: HttpClient) { }

  /**
   * gibt ein JSON Objekt mit allen Netzbetreibern zurück / returns a JSON object with all network operators
   * (id,name)

   * @returns{any}
   */

  // not used
  getNetworkOperator(): any {
    return this.http.get('/apiV2/netzbetreiber');
  }

  // get all Network Operators
  getNetworkOperators(data: any): any {
    return this.http.get('/apiV2/netzbetreiber/getAll', data);
  }

  getNetworkOperatorNames(data: any): any {
    return this.http.get('/apiV2/netzbetreiber/getAllNames', data);
  }

  // Contracts
  getNetworkOperatorContracts(data: any): any {
    return this.http.post('/apiV2/netzbetreiberr/getAllContracts', data);
  }

  addNetworkOperator(data: any): any {
    return this.http.post('/apiV2/netzbetreiber/createNetworkOperator', data);
  }

  editNetworkOperator(data: any): any {
    return this.http.post('/apiV2/netzbetreiber/updateNetworkOperator', data);
  }

  deleteNetworkOperator(id: string): any {
    return this.http.get('/apiV2/netzbetreiber/deleteNetworkOperator/' + id);
  }

  activateNetworkOperator(data: any): any {
    return this.http.post('/apiV2/netzbetreiber/activate', data);
  }

  inActivateNetworkOperator(data: any): any {
    return this.http.post('/apiV2/netzbetreiber/deactivate', data);
  }

  getNetworkOperatorStores(data: any): any {
    return this.http.get('/apiV2/netzbetreiberr/getAllStores', data);
  }

}
