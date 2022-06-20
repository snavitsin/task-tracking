<template>
  <div class="customers-page">
    <div
    v-text="'Заказчики'"
    class="customers-page__title" />
    <div class="customers-page__content">
      <div
      v-for="customer in customersData"
      :key="customer.customer_id"
      class="customers-page__subdiv">

        <div class="customers-page__subdiv-title">
          <a
          v-text="customer.customer_fio"
          :href="getCustomerUrl(customer)"
          target="_blank"
          class="customers-page__subdiv-link" />
        </div>
        <div
        v-text="getProjectCountText(customer)"
        class="customers-page__subdiv-count" />
      </div>
    </div>
  </div>
</template>

<script>


export default {
  name: "CustomersPage",
  components: { },
  props: {
    customers: { type: Array, default: () => [] },
  },
  data() {
    return {
      customersData: this.customers,
    }
  },
  methods: {
    getProjectCountText(customer) {
      return `Проекты: ${customer.customer_projects.length}`;
    },

    getCustomerUrl(customer) {
      return `/customers/${customer.customer_id}`;
    },

  },
}
</script>

<style lang="scss">
.customers-page {

  --accent-color: #906fe9;

  > * + * {
    margin-top: 20px;
  }

  &__title {
    color: #906fe9;
    font-weight: bold;
    font-size: 24px;
  }

  &__content {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    grid-auto-rows: 1fr;
    gap: 20px;
  }

  &__subdiv {
    padding: 15px;
    border: 3px solid var(--input-border-color, #906fe9);
    border-radius: 5px;
    box-shadow: 0 0 4px 4px rgb(154 161 177 / 15%), 0 4px 4px 1px rgb(91 94 105 / 15%), 0 4px 4px -2px rgb(91 94 105 / 15%);

    &-title {
      padding: 10px;
      background: #fff;
      border: 1px solid var(--input-border-color, #D3D8DB);
    }

    &-link {
      display: block;
      color: #1fe09e;
      cursor: pointer;
      font-weight: bold;

      &:hover {
        color: inherit;
      }
    }

    &-desc {
      padding: 10px;
      background: #fff;
      border: 1px solid var(--input-border-color, #D3D8DB);
    }

    > * + * {
      margin-top: 10px;
    }
  }

  &__title {
    color: #906fe9;
    font-weight: bold;
    font-size: 24px;
  }
}
</style>