import { Component, OnInit, ViewChild } from '@angular/core';
import { MatDialog } from '@angular/material/dialog';
import { MatPaginator } from '@angular/material/paginator';
import { MatSnackBar } from '@angular/material/snack-bar';
import { MatTableDataSource } from '@angular/material/table';
import { NetworkOperatorService } from '../services/network-operator.service';
import { NetworkOperatorDialogComponent } from '../network-operator-dialog/network-operator-dialog.component';

@Component({
  selector: 'app-network-operator',
  templateUrl: './network-operator.component.html',
  styleUrls: ['./network-operator.component.css']
})

export class NetworkOperatorComponent implements OnInit {

  contentLoading = true;
  admin: string;

  // Determine which columns should be visible in the table
  // 'abgeschlossen', 'del'
  displayedColumns: string[] = ['edit', 'name', 'vvl', 'contract', 'existingTariff', 'tariff', 'activate'];
  dataSource = new MatTableDataSource();

  @ViewChild(MatPaginator, { static: true }) set matPaginator(paginator: MatPaginator) {
    this.dataSource.paginator = paginator;
  }

  public inputValue: string;
  constructor(private _networkOperatorService: NetworkOperatorService,
    private dialog: MatDialog,
    private snackBar: MatSnackBar
  ) {
  }

  ngOnInit() {
    this.getAll();
    this.admin = localStorage.getItem('user');
    this.admin = JSON.parse(this.admin).admin;
  }

  getAll() {
    this._networkOperatorService.getNetworkOperators({}).subscribe(data => {
      if (data['status'] === 'success') {
        this.dataSource.data = data['data'];
      }
      this.contentLoading = false;
    });
  }

  showEditDialogAsAdd() {
    const editFields = [
      { inputLabel: 'Name', inputType: 'text', index: 'netz_name', required: true }
    ];

    const copy = {
      'netz_name': ''
    };

    const dialogRef = this.dialog.open(NetworkOperatorDialogComponent, {
      data: { title: 'Neuer Netzbetreiber', copy, editFields }
    });
    dialogRef.afterClosed().subscribe(result => {
      if (result) { this.confirmAdd(result); }
    });
  }
  confirmAdd(networkOperator) {
    const changeEntry = {
      'netz_name': networkOperator.netz_name
    };

    this._networkOperatorService.addNetworkOperator(changeEntry).subscribe(result => {
      if (result['status'] === 'success') {
        this.openSnackBar('Netzbetreiber erfolgreich hinzugefügt', '');
        this.getAll();
      }
    });
  }

  showEditDialog(networkOperator) {
    console.log("networkOperator:(showEditDialog start here) ", networkOperator);
    const editFields = [
      { inputLabel: 'Name', inputType: 'text', index: 'netz_name' },
      { inputLabel: 'Vertragsdauer', inputType: 'text', index: 'netz_vertragsdauer' },
      { inputLabel: 'VVL-berechtigt', inputType: 'text', index: 'netz_vvl' },
      { inputLabel: 'Kürzel', inputType: 'text', index: 'netz_kurz' },
      { inputLabel: 'VO Nummer', inputType: 'text', index: 'netz_vo' },
      { inputLabel: 'Notiz', inputType: 'textarea', index: 'netz_bemerkung' },
      { inputLabel: 'Keine Marge für Mitarbeiter berechnen', inputType: 'checkbox', index: 'netz_nomitmarge' }
    ];

    const copy = JSON.parse(JSON.stringify(networkOperator));
    console.log("copy: ", copy);
    const dialogRef = this.dialog.open(NetworkOperatorDialogComponent, {
      data: { title: 'Netzbetreiber bearbeiten', copy, editFields }
    });

    dialogRef.afterClosed().subscribe(result => {
      console.log("result: ", result);
      if (result) { this.confirmEdit(result); }
    });
  }
  confirmEdit(networkOperator) {
    console.log("networkOperator:(confirmEdit start here) ", networkOperator);
    const changeEntry = {
      'netz_name': networkOperator.netz_name,
      'netz_vertragsdauer': networkOperator.netz_vertragsdauer,
      'netz_vvl': networkOperator.netz_vvl,
      'netz_kurz': networkOperator.netz_kurz,
      'netz_vo': networkOperator.netz_vo,
      'netz_bemerkung': networkOperator.netz_bemerkung,
      'netz_nomitmarge': networkOperator.netz_nomitmarge,
      'all': networkOperator.all,
      'id': networkOperator.netz_id
    };

    console.log("changeEntry: ", changeEntry);
    this._networkOperatorService.editNetworkOperator(changeEntry).subscribe(result => {
      console.log("we are here!");
      console.log("confirmEdit result: ", result);
      if (result['status'] === 'success') {
        this.openSnackBar('Netzbetreiber erfolgreich geändert', '');
        this.getAll();
      }
    });

  }

  // zeigt kleines info Fenster unten an / shows little info window below
  openSnackBar(message: string, action: string) {
    this.snackBar.open(message, action, {
      duration: 5000
    });
  }

  clickButton(data) {
    const changeEntry = {
      'id': data.netz_id
    };

    // if it is 1 we will deactivate, if it is 0 we will activate
    if (data.active == 0) {
      this._networkOperatorService.activateNetworkOperator(changeEntry).subscribe(result => {
        if (result['status'] === 'success') {
          this.openSnackBar(data.netz_name + ' activated', '');
        }
      });
    }
    else {
      this._networkOperatorService.inActivateNetworkOperator(changeEntry).subscribe(result => {
        if (result['status'] === 'success') {
          this.openSnackBar(data.netz_name + ' deactivated', '');
        }
      });
    }
  }

}
