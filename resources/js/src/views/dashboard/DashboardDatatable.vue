<template>
<div>
   <FilterDialog  v-bind:selectedTable="selectedTable"  v-bind:colsWithFields="colsWithFields" v-model="showFilterDialog" @filter = "filter" />
  <v-card class="mt-5 px-3 py-3">

 <v-select
      v-model="selectedTable"
      :items="tables"
      :server-items-length="totalDatas"
      label="Choisir un rapport"
      @change="fetchHeaders"
      clearable
    ></v-select>
  <v-data-table

    dense
    :headers="tableHeader"
    :items="tableDatas"
    :page.sync="page"
    :items-per-page="itemsPerPage"
    :search="search"
    @page-count="pageCount = $event"
    hide-default-footer
    item-key="id"
    class="elevation-1 px-3 py-3"
  >

  <template v-slot:top>
      <v-toolbar
        flat
      >
        <v-toolbar-title>
           <v-text-field
           v-model="search"
          append-icon="mdi-magnify"
          :label="textSearch"
          single-line
          hide-details
        >

        </v-text-field>
        </v-toolbar-title>
        <v-divider
          class="mx-4"
          inset
          vertical
        ></v-divider>
        <v-spacer></v-spacer>
        <v-dialog
          v-model="dialog"
          max-width="500px"
        >
          <template v-slot:activator="{ on, attrs }">

            <v-btn
              color="primary"
              dark
              class="mb-2"
              @click="go(),showFilterDialog = true"
              v-show="selectedTable"
            >
              Filtrer la table
            </v-btn>



              <vue-json-to-csv
     :json-data="tableDatas"
     :csv-title="selectedTable"
>

     <v-btn color="secondary" dark class="mb-2 mx-2">
          Exporter en csv <i class="mdi mdi-export-variant" aria-hidden="true"></i>
     </v-btn>
</vue-json-to-csv>
          </template>

          <v-card>
            <v-card-title>
              <span class="text-h5">{{ formTitle }}</span>
            </v-card-title>

            <v-card-text>
              <v-container>
                <v-row

                >
                  <v-col
                    cols="12"
                    sm="6"
                    md="4"
                    v-for="(item,propertyName) in editedItem" v-bind:key="propertyName"
                  >
                  {{propertyName}}
                    <v-text-field
                      v-model="editedItem[propertyName]"
                    ></v-text-field>

                  </v-col>

                </v-row>
              </v-container>
            </v-card-text>

            <v-card-actions>
              <v-spacer></v-spacer>
              <v-btn
                color="blue darken-1"
                text
                @click="close, isNew = false"
              >
                Cancel
              </v-btn>
              <v-btn
                color="blue darken-1"
                text
                @click="save"
              >
                Save
              </v-btn>
            </v-card-actions>
          </v-card>
        </v-dialog>
        <v-dialog v-model="dialogDelete" max-width="500px">
          <v-card>
            <v-card-title class="text-h5">Are you sure you want to delete this item?</v-card-title>
            <v-card-actions>
              <v-spacer></v-spacer>
              <v-btn color="blue darken-1" text @click="closeDelete">Cancel</v-btn>
              <v-btn color="blue darken-1" text @click="deleteItemConfirm">OK</v-btn>
              <v-spacer></v-spacer>
            </v-card-actions>
          </v-card>
        </v-dialog>
      </v-toolbar>
    </template>
    <template v-slot:[`item.actions`]="{ item }">
      <v-icon
        small
        @click="deleteItem(item)"
      >
       {{ icons.mdiDelete }}
      </v-icon>
       <v-icon
        small
        @click="editItem(item)"
      >
              {{ icons.mdiPencil }}
      </v-icon>
    </template>
    <template v-slot:no-data>
      <v-btn
        color="primary"
      >
        Reset
      </v-btn>
    </template>


  </v-data-table>
 <div class="text-center pt-2">
      <v-pagination
        v-model="page"
        :length="pageCount"
      ></v-pagination>
      <v-text-field
        :value="itemsPerPage"
        label="Items per page"
        type="number"
        min="-1"
        max="15"
        @input="itemsPerPage = parseInt($event, 10)"
      ></v-text-field>
    </div>

  </v-card>
</div>
</template>

<script>
import { mdiPencil, mdiDelete } from '@mdi/js'
import data from './datatable-data'
import axios from 'axios'
import VueJsonToCsv from 'vue-json-to-csv'
import FilterDialog from './components/FilterDialog.vue'
import { dataMixins } from './../../Mixins/mixin.js';





export default {

  mixins: [dataMixins],

  components:{
    VueJsonToCsv,
    FilterDialog
  },

  props: {
     tablesNames : Array,
     reportsView: Boolean
  },

   data() {
    return {
       icons: {
        mdiPencil,
        mdiDelete,

      },
      isNew:false,
      dialog: false,
      dialogDelete: false,
      page: 1,
      pageCount: 0,
      itemsPerPage: 10,
      selectedTable:'',
      tables: [],
      totalDatas:0,
      userList: data,
      columnNames : [],
      status: '',
      editedIndex: -1,
      editedItem:{},
      defaultItem:{},
      textSearch: 'Rechercher...',
      search:'',
      showFilterDialog: false,
      colsWithFields: [],

    }
  },

  computed: {

      formTitle () {
        return this.editedIndex === -1 ? 'New Item' : 'Edit Item'
      },

    },

    watch: {

      dialog (val) {
        val || this.close()
      },
      dialogDelete (val) {
        val || this.closeDelete()
      },

      tables(){
        if (this.tablesNames.length != 0 ) {
          console.log('ene action',this.tablesNames );
          return this.tablesNames
        }
        this.fectchAllTablesNames
      }

    },


  methods: {


    fetchHeaders() {

        axios.get('/api/columnsnames', {
            params: {
            tableName: this.selectedTable
            }
          })
        .then((response) => {
          console.log('les headers : ',response)
          this.columnNames = response.data.header;

          this.tableHeader = Object.assign({}, response.data.header);
          this.colsWithFields = response.data.colsWithFields;
          console.log('Colones et leur Type : ',  this.colsWithFields );
          this.tableHeader =  this.createHeader(this.tableHeader);
          this.tableHeader.push({
            text: 'Actions',
            value: 'actions',
            sortable: false
          });

         console.log('fetchHeaders :', this.tableHeader);

        })
          this.fetchTablesData()

    },

    filter(datas){

       console.log('updateDatas en action : ', datas);
       this.tableDatas  =  datas.slice()

    },


    fectchAllTablesNames(){
          axios.get('/api/alltables')
          .then((response) => {
            console.log('fectchAllTablesNames : ', response.data.tables)
            this.tables = response.data.tables
     })

    },

    fetchTablesData(){

        axios.get('api/tabledatas',{
              params :{
                tableName : this.selectedTable,
                perPage: '100000000000000000000',
                currentPage: '1',
              }
            })
            .then((response) => {
              console.log('fetchTablesData :',response.data);

              this.tableDatas = response.data.tableDatas.data
              this.totalDatas = response.data.tableDatas.total

              console.log(typeof(this.tableDatas));

              this.editedItem = Object.assign({}, this.tableDatas[1]);
              Object.keys(this.editedItem).forEach((i) => this.editedItem[i] = '');
              this.defaultItem = this.editedItem
              console.log('le edit item: ', this.editedItem);

          })

    },

    createHeader(param){

       const objArray = [];
      Object.keys(param).forEach(key => objArray.push({
        text: param[key],
        value: param[key]
      }));
     param = objArray

  return param
     },

    editItem (item) {

        this.editedIndex = this.tableDatas.indexOf(item)
        this.editedItem = Object.assign({}, item)
         console.log('log de item', this.editedItem);
        this.dialog = true

      },



    deleteItem (item) {
        this.editedIndex = this.tableDatas.indexOf(item)
        this.editedItem = Object.assign({}, item)
        this.dialogDelete = true
      },

    deleteItemConfirm () {
        this.tableDatas.splice(this.editedIndex, 1)
        this.closeDelete()
      },

    close () {
        this.dialog = false
        this.$nextTick(() => {
          this.editedItem = Object.assign({}, this.defaultItem)
          this.editedIndex = -1
        })
      },

    closeDelete (){
        this.dialogDelete = false
        this.$nextTick(() => {
          this.editedItem = Object.assign({}, this.defaultItem)
          this.editedIndex = -1
        })
      },

    save () {
       const newItem =  this.editedItem
        axios.post(`api/editItem/${this.selectedTable}`,newItem)
        .then((response)=>{
          console.log(response);
        })

        if (this.editedIndex > -1) {
          Object.assign(this.tableDatas[this.editedIndex], this.editedItem)
        } else {
          this.tableDatas.push(this.editedItem)
        }
        this.close()
      },

      go(){
      }
  },

    created(){
      this.fectchAllTablesNames()

  }

}
</script>
