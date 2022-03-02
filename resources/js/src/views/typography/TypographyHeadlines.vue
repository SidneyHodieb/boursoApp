<template>
<v-row>
   <v-col
      cols="12"
      class="mb-6"
    >
  <v-card >
    <v-card-title>Champs de Requète SQL </v-card-title>
    <v-row class="ma-0 pb-5 px-2">
      <v-col
        cols="11"
        sm="10"

      >


        <codemirror
    ref="cmEditor"
    :value="code"
    :options="cmOptions"
    @ready="onCmReady"
    @focus="onCmFocus"
    @input="onCmCodeChange"
  />
      </v-col>
      <v-col></v-col>
      <v-row>
         <v-btn
              color="primary"
              dark
              class="mb-2"
              @click="sendQuery()"
            >
             Exécuter
            </v-btn>
      </v-row>
    </v-row>
  </v-card>
   </v-col>
   <v-col cols="12">
       <v-data-table

     dense
    :headers="tableHeader"
    :items="tableDatas"
    :page.sync="page"
    :items-per-page="itemsPerPage"
    @page-count="pageCount = $event"
    hide-default-footer
    item-key="id"
    class="elevation-1 px-3 py-3"

  >

  <template v-slot:top>
      <v-toolbar
        flat
      >
        <v-toolbar-title>Résultat</v-toolbar-title>
        <v-divider
          class="mx-4"
          inset
          vertical
        ></v-divider>
        <v-spacer></v-spacer>
         <vue-json-to-csv
       v-if="tableHeader"
     :json-data="tableDatas"
     csv-title="new_extraction"
>
     <v-btn color="success" dark class="mb-2 mx-2">
          Exporter en csv <i class="mdi mdi-export-variant" aria-hidden="true"></i>
     </v-btn>
</vue-json-to-csv>
      </v-toolbar>

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
   </v-col>
  </v-row>
</template>


<script>
import axios from 'axios'

import { codemirror } from 'vue-codemirror'

// import base style
import 'codemirror/lib/codemirror.css'
import 'codemirror/theme/dracula.css'
import 'codemirror/mode/sql/sql.js'
import 'codemirror/addon/hint/sql-hint';
import 'codemirror/addon/hint/anyword-hint';
import 'codemirror/theme/base16-dark.css';
import 'codemirror/addon/hint/show-hint.css';
import 'codemirror/lib/codemirror';
import 'codemirror/addon/hint/show-hint';
import 'codemirror/addon/search/searchcursor';
import 'codemirror/addon/search/search';
import 'codemirror/addon/display/placeholder';
import 'codemirror/addon/hint/sql-hint';
import 'codemirror/addon/hint/anyword-hint';
import VueJsonToCsv from 'vue-json-to-csv'



export default {
  components: {
    codemirror,
    VueJsonToCsv
  },
  data(){
    return{
      code: '',
      cmOptions: {
        lineNumbers: true,
        indentUnit: 4,
        autofocus: true,
        styleActiveLine: true,
        matchBrackets: true,
        mode: 'text/x-mysql',
        lineWrapping: true,
        theme: 'dracula',
        extraKeys: {
          Ctrl: 'autocomplete',
        },
        foldGutter: true,
        placeholder: "Votre requete ici ...",
      },
       page: 1,
      pageCount: 0,
      itemsPerPage: 10,
      tableHeader:[],
      tableDatas:[],

    }
  },

   computed: {
    codemirror() {
      return this.$refs.cmEditor.codemirror
    }
  },

  methods :{
    sendQuery(){
      const data = {
      requete : this.code
    }
    axios.post('api/runQuery/',data)
        .then((response)=>{
          /* console.log(response.data.tables[0]); */
          this.tableDatas = response.data.tables
          this.tableHeader = Object.assign({},  response.data.tables[0]);
          Object.keys(this.tableHeader).forEach((i) => this.tableHeader[i] = '');
           this.tableHeader =  this.createHeader(this.tableHeader);
          console.log(this.tableHeader);
        })
    },

    createHeader(param){

       const objArray = [];
      Object.keys(param).forEach(key => objArray.push({
        text: key,
        value: key
      }));


  return objArray
     },

     onCmReady(cm) {
        cm.on('keypress', () => {
          cm.showHint()
      })
    },
    onCmFocus(cm) {
      console.log('the editor is focused!', cm)
    },
    onCmCodeChange(newCode) {
      console.log('this is new code', newCode)
      this.code = newCode
    }
  },

  created(){


  }
}
</script>
