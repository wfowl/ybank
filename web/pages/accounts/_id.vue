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
        <b-table striped hover :items="transactions" :fields="transaction_fields"></b-table>
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

      errors: [],
      transaction_fields: [
        'id', 'from', 'to', 'details', 'money_to', 'money_from'
      ]
    };
  },

  mounted() {
    this.fetchAccountInfo();
  },

  methods: {
    onSubmit(evt) {
      evt.preventDefault();

      axios.post(
        `http://localhost:8000/api/accounts/${
          this.$route.params.id
        }/transactions`,

        this.payment
      )
      .then(() => {
        this.errors = [];

        this.payment = {};
        this.show = false;

        // update items
        setTimeout(() => {
          this.fetchAccountInfo();
        }, 200);
      })
      .catch(function (error) {
        for(let prop in error.response.data) {
          error.response.data[prop].forEach(element=> {
            this.errors.push(element);
          })
        }
      }.bind(this))
      
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
          this.currencySymbol = response.data.find(function(currency) {
            return currency.id == this.account.country.currency_id;
          }.bind(this)).symbol;

          let toCurrencySymbol = response.data.find(function(currency) {
            return currency.id == transaction.to_currency_id;
          }.bind(this)).symbol;

          let fromCurrencySymbol = response.data.find(function(currency) {
            return currency.id == transaction.from_currency_id;
          }.bind(this)).symbol;

          transaction.money_from = fromCurrencySymbol + transaction.money_from;
          transaction.money_to = toCurrencySymbol + transaction.money_to;

          delete transaction.to_currency_id;
          delete transaction.from_currency_id;
          
        });
      }.bind(this));
    },

    fetchTransactions() {
      axios
      .get(
        `http://localhost:8000/api/accounts/${
          this.$route.params.id
        }/transactions`
      )
      .then(function(response) {
        this.transactions = response.data;

        if (this.account && this.transactions) {
          this.loading = false;
        }
        this.fetchCurrencyInfo();
      }.bind(this))
    },

    fetchAccountInfo() {
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

        this.fetchTransactions();
      }.bind(this));
    }
  }
};
</script>
