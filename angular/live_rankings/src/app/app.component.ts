import { Component } from '@angular/core';
import { RouterOutlet } from '@angular/router';

import {CommonModule} from '@angular/common';
import { HttpClientModule,HttpClient } from '@angular/common/http';
import { FormsModule,ReactiveFormsModule } from '@angular/forms';



@Component({
  selector: 'app-root',
  standalone: true,
  imports: [RouterOutlet, CommonModule, HttpClientModule, FormsModule, ReactiveFormsModule],
  templateUrl: './app.component.html',
  styleUrl: './app.component.css'
})
export class AppComponent {
  title = 'live_rankings';

  limit = 10;
  ranking:any = [];

  APIURL="http://localhost:8000/";
  constructor(private http: HttpClient) {}
  

  ngOnInit() {
    this.getDishRanking();
  }

  getDishRanking() {

    this.http.get(this.APIURL+'get_dish_ranking').subscribe((res) => {
      this.ranking=res;
    })
}}
