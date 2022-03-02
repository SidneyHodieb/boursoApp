 export const dataMixins = {
     data () {
       return{
        tableHeader:[],
        tableDatas:[],
        msg:'yoo',
       }

     },
     created: function () {
     },
     methods: {
      createHeader(param){

        const objArray = [];
       Object.keys(param).forEach(key => objArray.push({
         text: key,
         value: key
       }));


   return objArray
      },
     }
  }

