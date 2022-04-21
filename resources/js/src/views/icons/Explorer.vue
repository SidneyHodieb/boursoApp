<template>
  <v-card>
    <v-card-title class="indigo white--text text-h5">
     Explorateur de rapports {{selected}} {{active}}
    </v-card-title>
    <v-row
      class="pa-4"
      justify="space-between"
    >
      <v-col cols="5">

        <v-treeview
          :active.sync="active"
          :items="dir"
          item-key="code"
          :load-children="fetchReports"
          :open.sync="open"
          activatable
          color="warning"
          open-on-click
          transition
        >
        <template v-slot:prepend="{ open }">
      <v-icon>
        {{ open ? 'mdi-folder-open' : 'mdi-folder' }}
      </v-icon>

    </template>
        </v-treeview>
      </v-col>

      <v-divider vertical></v-divider>

      <v-col
        class="d-flex text-center"
      >
        <v-scroll-y-transition mode="out-in">
          <div
            v-if="!selected"
            class="text-h6 grey--text text--lighten-1 font-weight-light"
            style="align-self: center;"
          >
           <DataTable  v-if="dataLoaded" @reportsView='reportsView' @tablesNames = "tablesNames" />
          </div>
          <v-card
            v-else
            class="pt-6 mx-auto"
            flat
            max-width="400"
          >
            <v-card-text>
              <v-avatar
                size="88"
              >
                <v-img
                  :src="`https://avataaars.io/${avatar}`"
                  class="mb-6"
                ></v-img>
              </v-avatar>
                <DataTable @tablesNames = "tablesNames" />
            </v-card-text>
            <v-divider></v-divider>
          </v-card>
        </v-scroll-y-transition>
      </v-col>
    </v-row>
  </v-card>
</template>


<script>
import DataTable from '../dashboard/DashboardDatatable.vue'
import {
  mdiHomeOutline,
  mdiDatabaseSearch,
  mdiEyeOutline,
  mdiCreditCardOutline,
  mdiTable,
  mdiFileOutline,
  mdiFormSelect,
  mdiAccountCogOutline,
  mdiFolderOpen,
  mdiFolder,
} from '@mdi/js'
import axios from 'axios'
  const avatars = [
    '?accessoriesType=Blank&avatarStyle=Circle&clotheColor=PastelGreen&clotheType=ShirtScoopNeck&eyeType=Wink&eyebrowType=UnibrowNatural&facialHairColor=Black&facialHairType=MoustacheMagnum&hairColor=Platinum&mouthType=Concerned&skinColor=Tanned&topType=Turban',
    '?accessoriesType=Sunglasses&avatarStyle=Circle&clotheColor=Gray02&clotheType=ShirtScoopNeck&eyeType=EyeRoll&eyebrowType=RaisedExcited&facialHairColor=Red&facialHairType=BeardMagestic&hairColor=Red&hatColor=White&mouthType=Twinkle&skinColor=DarkBrown&topType=LongHairBun',
    '?accessoriesType=Prescription02&avatarStyle=Circle&clotheColor=Black&clotheType=ShirtVNeck&eyeType=Surprised&eyebrowType=Angry&facialHairColor=Blonde&facialHairType=Blank&hairColor=Blonde&hatColor=PastelOrange&mouthType=Smile&skinColor=Black&topType=LongHairNotTooLong',
    '?accessoriesType=Round&avatarStyle=Circle&clotheColor=PastelOrange&clotheType=Overall&eyeType=Close&eyebrowType=AngryNatural&facialHairColor=Blonde&facialHairType=Blank&graphicType=Pizza&hairColor=Black&hatColor=PastelBlue&mouthType=Serious&skinColor=Light&topType=LongHairBigHair',
    '?accessoriesType=Kurt&avatarStyle=Circle&clotheColor=Gray01&clotheType=BlazerShirt&eyeType=Surprised&eyebrowType=Default&facialHairColor=Red&facialHairType=Blank&graphicType=Selena&hairColor=Red&hatColor=Blue02&mouthType=Twinkle&skinColor=Pale&topType=LongHairCurly',
  ]



  const pause = ms => new Promise(resolve => setTimeout(resolve, ms))

  export default {

    components : {
      DataTable
    },

     setup() {
    return {
      icons: {
        mdiHomeOutline,
        mdiDatabaseSearch,
        mdiEyeOutline,
        mdiCreditCardOutline,
        mdiTable,
        mdiFileOutline,
        mdiFormSelect,
        mdiAccountCogOutline,
        mdiFolderOpen,
        mdiFolder,
      },
    }
  },

    data: () => ({
      active: [],
      avatar: null,
      open: [],
      users: [],
      directories: [],
      tablesNames : [],
      tablesFetched :[],
      reportsView: false,
      dataLoaded : false,
    }),

    computed: {
      items () {
          return [
          {
            name: 'Users',
            children: [
              {
                  name: 'src ',
                  children:
                  this.users,

                },
            ]
          },
        ]
      },
      dir(){

        return  this.directories

      },
      selected () {
        if (!this.active.length) return undefined

        const name = this.active[0]

        return this.users.find(user => user.id === id)
      },
    },

    watch: {
      selected: 'randomAvatar',
      active :  'fetchReports',
    },

    methods: {
      async fetchUsers (item) {
      /*   // Remove in 6 months and say
        // you've made optimizations! :)
        await pause(1500)

        return fetch('https://jsonplaceholder.typicode.com/users')
          .then(res => res.json())
          .then(json => (item.children.push(...json)))
          .catch(err => console.warn(err)) */
      },

      randomAvatar () {
        this.avatar = avatars[Math.floor(Math.random() * avatars.length)]
      },



      fetchDirectories(){

            axios.get('/api/alldirectories')
              .then((response) => {
                console.log('alldirectories : ', response.data)
                this.directories = response.data.directories
               /*  console.log(this.directories);
                console.log(this.items); */
        })

      },

      fetchReports(){

        this.dataLoaded = false

       axios.get('/api/reportsnames', {
            params: {
            code: this.active[0]
            }
          })
        .then((response) => {
          console.log('fetchReports : ',response)
          this.tablesNames = response.data.tables
          if ( this.tablesNames.length != 0 ) {
            console.log('le legnht : ',this.tablesNames);
            this.dataLoaded = true
          }

          this.reportsView = true
          })


      }



    },

   created() {
     this.fetchDirectories()
    }
  }
</script>

