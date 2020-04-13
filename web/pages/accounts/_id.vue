<template>
  <div>
    <div class="container" v-if="loading">loading...</div>

    <div class="container" v-if="!loading">
      <b-card :header="'Welcome, ' + account.name" class="mt-3">
        <b-card-text>
          <div>
            Country: <code>{{ account.country.name }}</code>
          </div>
          <div>
            Account: <code>{{ account.id }}</code>
          </div>
          <div>
            Balance:
            <code
              >{{ currencySymbol }}{{ account.balance }}</code
            >
          </div>
        </b-card-text>
        <b-button size="sm" variant="success" @click="show = !show"
          >New payment</b-button
        >

        <b-button
          class="float-right"
          variant="danger"
          size="sm"
          nuxt-link
          to="/"
          >Logout</b-button
        >
      </b-card>

      <b-card class="mt-3" header="Errors" v-show="errors.length > 0">
        <ul>
          <li v-for="error in errors" style="color:red">
            {{ error }}
          </li>
        </ul>
      </b-card>

      <b-card class="mt-3" header="New Payment" v-show="show">
        <b-form @submit="onSubmit">
          <b-form-group id="input-group-1" label="To:" label-for="input-1">
            <b-form-input
              id="input-1"
              size="sm"
              v-model="payment.to"
              type="number"
              required
              placeholder="Destination ID"
            ></b-form-input>
          </b-form-group>

          <b-form-group id="input-group-2" label="Amount:" label-for="input-2">
            <b-input-group prepend="$" size="sm">
              <b-form-input
                id="input-2"
                v-model="payment.amount"
                type="number"
                required
                placeholder="Amount"
              ></b-form-input>
            </b-input-group>
          </b-form-group>

          <b-form-group id="input-group-3" label="Details:" label-for="input-3">
            <b-form-input
              id="input-3"
              size="sm"
              v-model="payment.details"
              required
              placeholder="Payment details"
            ></b-form-input>
          </b-form-group>

          <b-button type="submit" size="sm" variant="primary">Submit</b-button>
        </b-form>
      </b-card>

      <b-card class="mt-3" header="Payment History">
        <b-table striped hover :items="transactions"></b-table>
      </b-card>
    </div>
  </div>
</template>

<script lang="ts">
import axios from "axios";
import Vue from "vue";

export default {
  data() {
    return {
      show: false,
      payment: {},

      account: null,
      transactions: null,

      loading: true,
      currencySymbol: '',

      errors: []
    };
  },

  mounted() {
    axios
      .get(`http://localhost:8000/api/accounts/${this.$route.params.id}`)
      .then(function(response) {
        if (!response.data.length) {
          window.location = "/";
        } else {
          this.account = response.data[0];

          if (this.account && this.transactions) {
            this.loading = false;
          }
        }
      }.bind(this));

    axios
      .get(
        `http://localhost:8000/api/accounts/${
          this.$route.params.id
        }/transactions`
      )
      .then(function(response) {
        this["transactions"] = response.data;

        var transactions = [];
        for (let i = 0; i < this.transactions.length; i++) {
          this.transactions[i].amount =
            this.currencySymbol +
            this.transactions[i].amount;

          if (this.account.id != this.transactions[i].to) {
            this.transactions[i].amount = "-" + this.transactions[i].amount;
          }

          transactions.push(this.transactions[i]);
        }

        this.transactions = transactions;

        if (this.account && this.transactions) {
          this.loading = false;
        }
        this.fetchCurrencyInfo();
      }.bind(this));
  },

  methods: {
    onSubmit(evt) {
      evt.preventDefault();

      axios.post(
        `http://localhost:8000/api/accounts/${
          this.$route.params.id
        }/transactions`,

        this.payment
      ).catch(function (error) {
        let errors = [];
        if(error.response.data.amount) {
          error.response.data.amount.forEach(element => {
            errors.push(element);
          });
        }

        if(error.response.data.to) {
          error.response.data.to.forEach(element => {
            errors.push(element);
          });
        }

        if(error.response.data.details) {
          error.response.data.details.forEach(element => {
            errors.push(element);
          });
        }
        
        if(error.response.data.id) {
          error.response.data.id.forEach(element => {
            errors.push(element);
          });
        }
        
        this.errors = errors

      }.bind(this))
      this.payment = {};
      this.show = false;

      // update items
      setTimeout(() => {
        axios
          .get(`http://localhost:8000/api/accounts/${this.$route.params.id}`)
          .then(function(response) {
            if (!response.data.length) {
              window.location = "/";
            } else {
              this.account = response.data[0];
            }
          }.bind(this));

        axios
          .get(
            `http://localhost:8000/api/accounts/${
              this.$route.params.id
            }/transactions`
          )
          .then(function(response) {
            this["transactions"] = response.data;

            var transactions = [];
            for (let i = 0; i < this.transactions.length; i++) {
              this.transactions[i].amount =
                this.currencySymbol +
                this.transactions[i].amount;

              if (this.account.id != this.transactions[i].to) {
                this.transactions[i].amount = "-" + this.transactions[i].amount;
              }

              transactions.push(this.transactions[i]);
            }

            this.transactions = transactions;
          }.bind(this));
      }, 200);
      
      

    },

    fetchCurrencyInfo() {
      axios.get(
        `http://localhost:8000/api/currencies`
      )
      .then(function(response) {
        this.currencySymbol = response.data.find(function(currency) {
          return currency.id == this.account.country.currency_id
        }.bind(this)).symbol

        this.transactions.forEach(transaction => {
          transaction.amount = this.currencySymbol + transaction.amount
        });
      }.bind(this));
    }
  }
};
</script>
