<template>
<v-row justify="center">
    <v-dialog
     v-model="show"
      fullscreen
      hide-overlay
      transition="dialog-bottom-transition"
    >
      <v-card>
        <v-toolbar
          dark
          color="primary"
        >
          <v-btn
            icon
            dark
            color="white"
            @click="show = false"
          >
            <v-icon> {{ icons.mdiClose }}</v-icon>
          </v-btn>
          <v-toolbar-title style="color:white">Affiner </v-toolbar-title>
          <v-spacer></v-spacer>
          <v-toolbar-items>
          <v-btn
              color="white" dark class="mt-3 mx-2"
              text
               @click="createQuery()"
            >
             Lancer
            </v-btn>
          </v-toolbar-items>
        </v-toolbar>
        <v-list
          three-line
          subheader
        >
          <v-subheader>Vous pouvez filtrer plusieurs colonnes en cliquant sur {{finalQuery}}  <v-icon color="black" size="30"> {{ icons.mdiPlus }}</v-icon></v-subheader>
          <v-row >


            <v-col cols="5">
          <v-list-item>
            <v-list-item-content>
              <v-list-item-title>Colonne</v-list-item-title>
              <v-list-item-subtitle>

                <v-autocomplete
            v-model="selectedColumn"
            :items="columns"
            @change="loadFilters()"
            dense
            filled
            label="Entrer le Nom de la colonne à filtrer"
          ></v-autocomplete>


          </v-list-item-subtitle>
            </v-list-item-content>
          </v-list-item>
           <v-list-item>
            <v-list-item-content>
              <v-list-item-title>Filtre</v-list-item-title>
                <v-list-item-subtitle>


                <v-autocomplete
            v-model="selectedFilter"
            :items="finalFilters"
            dense
            filled
            item-text="name"
            item-value="name"
            label="Selectionnez le type de Filtre"
          ></v-autocomplete>

          </v-list-item-subtitle>
            </v-list-item-content>
          </v-list-item>
            <v-list-item v-show="valueField">
            <v-list-item-content>
              <v-list-item-title>Valeur</v-list-item-title>
                <v-list-item-subtitle>
                  <v-text-field
            v-model="valueSearch"
            label="Entrez la valeur cible"
            solo

          ></v-text-field>
          </v-list-item-subtitle>
            </v-list-item-content>
          </v-list-item>
           </v-col>

                <v-col style="display:hidden" cols="5" offset-md="1">

                  <v-chip v-for="filter in filterList" :key="filter.id" style="border:none" class="pa-5" outlined close >
                                                 {{filter.condition}}
                <v-chip outlined color="primary" >{{filter.col}}</v-chip>
               <v-chip outlined color="warning">  {{filter.filterName}}   </v-chip>
                <v-chip outlined color="success" >{{filter.value}}</v-chip>
              </v-chip>

                </v-col>


           </v-row>


           <v-list-item>
            <v-list-item-content>
                <v-list-item-subtitle>
                  <v-row>
                    <v-col cols="1"></v-col>
      <v-col cols="2">
                 <v-btn
                 @click="createQuery()"
              depressed
              color="success"
            >
               Lancer la requète
              </v-btn>

      </v-col>
      <v-col>
         <v-progress-circular
      :size="50"
      color="primary"
      indeterminate
      v-show="load"
    ></v-progress-circular>
      </v-col>
       <v-col cols="2">
                <v-menu
      transition="slide-x-transition"
      bottom
      right
    >
      <template v-slot:activator="{ on, attrs }">
        <v-btn
        disabled
          @click="addInput"
          class="deep-orange"
          color="primary"
          dark
          v-bind="attrs"
          v-on="on"
        >
          Ajouter un filtre <v-icon>{{ icons.mdiPlus }}</v-icon>
        </v-btn>
      </template>

      <v-list shaped>
         <v-list-item-group

        v-model="selectedCondition"
        color="sucess"
      >
        <v-list-item
        value="ou"
        >
        <v-list-item-content>
          <v-list-item-title>Ou</v-list-item-title>
        </v-list-item-content>
        </v-list-item>
         <v-list-item value="et"
        >
          <v-list-item-title >Et</v-list-item-title>
        </v-list-item >
         </v-list-item-group>
      </v-list>
    </v-menu>
      </v-col>
      </v-row>
          </v-list-item-subtitle>
            </v-list-item-content>
          </v-list-item>
        </v-list>

        <v-divider></v-divider>
        <v-list
          three-line
          subheader
        >

          <v-list-item>
            <v-list-item-content>
              <v-list-item-title>
                <v-col
          cols="6"
          sm="6"
        >

        </v-col>
        </v-list-item-title>
              <v-list-item-subtitle></v-list-item-subtitle>
            </v-list-item-content>
          </v-list-item>
        </v-list>
      </v-card>
    </v-dialog>
  </v-row>
</template>

<script>
import { mdiClose, mdiPlus, mdiRocketLaunch, } from '@mdi/js'
import { dataMixins } from '../../../Mixins/mixin.js';
import axios from 'axios'

export default {
  mixins : [dataMixins],
  props: {
     value: Boolean,
     colsWithFields :Array,
     selectedTable: String,
  },

  data(){
    return{
       icons: {
        mdiClose,
        mdiPlus,
        mdiRocketLaunch,

      },
        dialog: false,
        notifications: false,
        sound: true,
        widgets: false,
        filters: [

          {name : 'Egal', value:'='},
          {name : 'Contient', value:'like'},
          {name : 'Différent de', value:'<>'},
          {name : 'Supérieur', value:'>'},
          {name : 'Inférieur', value:'<'},
          {name : 'Supérieur ou égal', value:'>='},
          {name : 'Inférieur ou égal', value:'<='},
          {name : 'Inclus dans', value:'in'},
          {name : 'Compris entre', value:'between'},
          {name : 'Est null', value:'IS NULL'},
          {name : "N'est pas null", value:'IS NOT NULL'},


        ],
        finalFilters : [],
        selectedFilter:'',
        selectedColumn:[],
        valueSearch:null,
        filter:'',
        filterList: [],
        counter:0,
        selectedCondition:'',
        finalQuery:'',
        load:false,



    }
  },

  computed: {
    show: {
      get () {
        return this.value
      },
      set (value) {
         this.$emit('input', value)
      }
    },
    columns: {

      get () {
          const objArray = [];
          const param = this.colsWithFields;
          for (var key in param){
              objArray.push(param[key].field)
          }
         console.log("fields.name:",objArray)
         return objArray
      },

    },


     tableName: {
      get () {
        return this.selectedTable
      },
    },

    valueField : {
      get(){
        console.log("selectedFilter.value",this.selectedFilter);
        if(this.selectedFilter == 'Est null' || this.selectedFilter == "N'est pas null" || this.selectedFilter =='Inclus dans'){
          return false
        }
        return true
      }

    }
  },

  methods :{

    loadFilters(){
        var columnType ='';
        const param = this.colsWithFields;
        for (var key in param){
          if(param[key].field == this.selectedColumn){
           columnType = param[key].type
          }
        }

           if(columnType == 'string'){
                this.finalFilters = [
                   {name : 'Egal', value:'like'},
                   {name : 'Différent de', value:'diff'},
                   {name : 'Inclus dans', value:'in'},
                   {name : 'Est null', value:'null'},
                   {name : "N'est pas null", value:'notNull'},
                ]
              }
              else{
                this.finalFilters = this.filters
              }
              this.finalFilters = this.filters
    },

    createQuery() {
     this.filter = this.filters.find(element => element.name == this.selectedFilter);

      this.valueSearch =  this.valueSearch.trim() //Remove space from the starts ans end of the searching value

      if ( this.selectedFilter == 'Inférieur ou égal' || this.selectedFilter == "Supérieur ou égal" || this.selectedFilter == "Inférieur" || this.selectedFilter == 'Supérieur') {
          var query = `SELECT * FROM ${this.selectedTable} WHERE ${this.selectedColumn} ${this.filter.value} ${this.valueSearch} `
      }
      else if (this.selectedFilter == 'Est null' || this.selectedFilter == "N'est pas null") {
          var query = `SELECT * FROM ${this.selectedTable} WHERE ${this.selectedColumn} ${this.filter.value} `
      }
     else if (this.selectedFilter == 'Contient') {
          var query = `SELECT * FROM ${this.selectedTable} WHERE ${this.selectedColumn} %${this.valueSearch}% `
      }
     else if (this.selectedFilter == 'Egal' || this.selectedFilter == 'Différent de') {
          var query = `SELECT * FROM ${this.selectedTable} WHERE ${this.selectedColumn} ${this.filter.value} '${this.valueSearch}' `
      }
     else if (this.selectedFilter == 'Inclus dans') {
         var query = `SELECT * FROM ${this.selectedTable} WHERE IN ${this.selectedColumn} ${this.filter.value} `
      }
      else{

      }
        this.finalQuery = query
        this.sendQuery()

    },

    sendQuery(){
      this.load = true
      const data = {
      requete : this.finalQuery
    }
    axios.post('api/runQuery/',data)
        .then((response)=>{
          console.log('runQuery filter : ',response);
          /* console.log(response.data.tables[0]); */
          this.tableDatas = response.data.tables
          this.tableHeader = Object.assign({},  response.data.tables[0]);
          Object.keys(this.tableHeader).forEach((i) => this.tableHeader[i] = '');
           this.tableHeader =  this.createHeader(this.tableHeader);

          console.log('data envoyée',this.tableDatas);
          this.$emit('filter', this.tableDatas)
           this.load = false
           this.show = false
        })
        .catch((error)=>{
          console.log(error);
            this.load = false
        })




    },

    addInput() {
      this.filterList.push({
        id:this.counter++,
        col: this.selectedColumn,
        filtername: this.selectedFilter,
        value: this.valueSearch,
        condition:this.selectedCondition

      });
    },


  },

  created(){

  }


}





</script>
