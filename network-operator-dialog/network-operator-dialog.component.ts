import { Component, OnInit, Inject } from "@angular/core";
import { MatDialogRef, MAT_DIALOG_DATA } from "@angular/material/dialog";
import { DateAdapter, MAT_DATE_FORMATS, MAT_DATE_LOCALE } from '@angular/material/core';
import { MomentDateAdapter, MAT_MOMENT_DATE_ADAPTER_OPTIONS } from '@angular/material-moment-adapter';
import * as moment from 'moment';

export const MY_FORMATS = {
  parse: {
    dateInput: 'DD.MM.YYYY',
  },
  display: {
    dateInput: 'DD.MM.YYYY',
    monthYearLabel: 'MMM YYYY',
    dateA11yLabel: 'DD',
    monthYearA11yLabel: 'MMM YYYY',
  },
};

@Component({
  selector: 'app-network-operator-dialog',
  templateUrl: './network-operator-dialog.component.html',
  styleUrls: ['./network-operator-dialog.component.css'],

  providers: [
    // `MomentDateAdapter` can be automatically provided by importing `MomentDateModule` in your
    // application's root module. We provide it at the component level here, due to limitations of
    // our example generation script.
    {
      provide: DateAdapter,
      useClass: MomentDateAdapter,
      deps: [MAT_DATE_LOCALE, MAT_MOMENT_DATE_ADAPTER_OPTIONS]
    },

    { provide: MAT_DATE_FORMATS, useValue: MY_FORMATS },
    { provide: MAT_DATE_LOCALE, useValue: 'de_DE' },
  ]
})


export class NetworkOperatorDialogComponent implements OnInit {

  files: File[] = [];

  constructor(public dialogRef: MatDialogRef<NetworkOperatorDialogComponent>,
    @Inject(MAT_DIALOG_DATA) public data: any) { }

  save() {
    const tmp = this.data.copy;
    let success = true;
    let addFiles = false;

    this.data.editFields.forEach(function (item) {
      if (item.hasOwnProperty('required')) {
        if (!tmp[item.index] && item.required) {
          success = false;
        }
      }

      if (item.hasOwnProperty('inputType')) {
        if (item.inputType === 'dropbox') {
          addFiles = true;
        }
      }
    });

    // @ts-ignore
    if (addFiles === true) {
      this.data.copy.icon = this.files;
    }

    if (success === true) {
      this.dialogRef.close(this.data.copy);
    }

  }

  saveTariff() {
    let tmp = this.data.copy;
    let success = true;
    // let addFiles = false;

    this.data.editFields.forEach(function (item) {
      if (item.hasOwnProperty('required')) {
        if (!tmp[item.index] && item.required) {
          success = false;
        }
      }

      if (item.hasOwnProperty('inputType')) {
        if (item.inputType === 'dropbox') {
          // addFiles = true;
        }
      }

    });

    this.data.copy.all = 1;

    // @ts-ignore
    // if (addFiles === true) {
    //   this.data.copy.icon = this.files;
    // }

    if (success === true) {
      this.dialogRef.close(this.data.copy);
    }
  }

  saveContract() {
    let tmp = this.data.copy;
    let success = true;

    this.data.editFields.forEach(function (item) {
      if (item.hasOwnProperty('required')) {
        if (!tmp[item.index] && item.required) {
          success = false;
        }
      }

      if (item.hasOwnProperty('inputType')) {
        if (item.inputType === 'dropbox') {
          // addFiles = true;
        }
      }

    });

    this.data.copy.all = 2;

    // @ts-ignore
    // if (addFiles === true) {
    //   this.data.copy.icon = this.files;
    // }

    if (success === true) {
      this.dialogRef.close(this.data.copy);
    }
  }

  /**
   * copies input start date in end date Input
   */
  fillEndDate() {
    this.data.copy.bhour_to_date = this.data.copy.bhour_from_date;
  }

  onSelect(event) {
    this.files.push(...event.addedFiles);
  }

  onRemove(event) {
    this.files.splice(this.files.indexOf(event), 1);
  }

  selectDuration(minutes) {
    let end;

    if (moment.isMoment(this.data.copy.bhour_to_date)) {
      end = moment(moment(this.data.copy.bhour_to_date).format('YYYY-MM-DD') + ' ' + this.data.copy.endTime);
    } else {
      end = moment(this.data.copy.bhour_to_date + ' ' + this.data.copy.endTime);
    }

    const start = end.subtract(minutes, 'minutes');
    this.data.copy.bhour_from_date = start.format('YYYY-MM-DD');
    this.data.copy.startTime = start.format('HH:mm');
  }


  ngOnInit() {
    console.log("data: ", this.data);
    // this.onNoClick();
  }

}
