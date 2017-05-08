<template>
    <div class="container">
        <div class="row">
            <div class="col-md-6 col-md-offset-6">
                <div @click="toggleEditable">{{user.name}}</div>
                <input v-model="user.username" type="text" v-bind:disabled="!editable"  />
                <input v-model="user.email" type="text" />
                <input v-model="user.password" type="text" />
                <input v-model="user.role" type="text" />
                <button class="btn btn-primary" @click="register" v-bind:disabled="loading" type="button">Register</button>
            </div>
            <div v-for="user in users">{{user.username}}</div>
        </div>
    </div>
</template>

<script>
    export default {
        data: function () {
            return {
                user: {
                    name: 'dhwanil'
                },
                users: [],
                loading: false,
                editable: false
            }
        },
        mounted() {
          axios.get('/api/users/').then(function (response) {
              this.users = response.data;
          }.bind(this));
        },
        methods: {
            register: function () {
                this.loading = true;
                axios.post('/api/users/create', this.user).then(function (response) {
                    console.log(response);
                    window.location.href = '/dashboard';
                    this.loading = false;
                }).catch(function (error) {
                    console.log(error);
                    this.loading = false;
                });
            },
            toggleEditable: function () {
                this.editable = !this.editable;
            }
        }
    }
</script>
