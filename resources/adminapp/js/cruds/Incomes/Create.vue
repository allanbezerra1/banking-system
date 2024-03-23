<template>
  <div class="container-fluid">
    <form @submit.prevent="submitForm">
      <div class="row">
        <div class="col-md-12">
          <div class="card">
            <div class="card-header card-header-primary card-header-icon">
              <div class="card-icon">
                <i class="material-icons">add</i>
              </div>
              <h4 class="card-title">
                {{ $t('global.create') }}
                <strong>{{ $t('cruds.income.title_singular') }}</strong>
              </h4>
            </div>
            <div class="card-body">
              <back-button></back-button>
            </div>
            <div class="card-body">
              <bootstrap-alert />
              <div class="row">
                <div class="col-md-12">
                  <div
                    class="form-group bmd-form-group"
                    :class="{
                      'has-items': entry.income_category_id !== null,
                      'is-focused': activeField == 'income_category'
                    }"
                  >
                    <label class="bmd-label-floating">{{
                      $t('cruds.income.fields.income_category')
                    }}</label>
                    <v-select
                      name="income_category"
                      label="name"
                      :key="'income_category-field'"
                      :value="entry.income_category_id"
                      :options="lists.income_category"
                      :reduce="entry => entry.id"
                      @input="updateIncomeCategory"
                      @search.focus="focusField('income_category')"
                      @search.blur="clearFocus"
                    />
                  </div>
                  <div
                    class="form-group bmd-form-group"
                    :class="{
                      'has-items': entry.user_id !== null,
                      'is-focused': activeField == 'user'
                    }"
                  >
                    <label class="bmd-label-floating">{{
                      $t('cruds.income.fields.user')
                    }}</label>
                    <v-select
                      name="user"
                      label="name"
                      :key="'user-field'"
                      :value="entry.user_id"
                      :options="lists.user"
                      :reduce="entry => entry.id"
                      @input="updateUser"
                      @search.focus="focusField('user')"
                      @search.blur="clearFocus"
                    />
                  </div>
                  <div
                    class="form-group bmd-form-group"
                    :class="{
                      'has-items': entry.entry_date,
                      'is-focused': activeField == 'entry_date'
                    }"
                  >
                    <label class="bmd-label-floating required">{{
                      $t('cruds.income.fields.entry_date')
                    }}</label>
                    <datetime-picker
                      class="form-control"
                      type="text"
                      picker="date"
                      :value="entry.entry_date"
                      @input="updateEntryDate"
                      @focus="focusField('entry_date')"
                      @blur="clearFocus"
                      required
                    >
                    </datetime-picker>
                  </div>
                  <div
                    class="form-group bmd-form-group"
                    :class="{
                      'has-items': entry.amount,
                      'is-focused': activeField == 'amount'
                    }"
                  >
                    <label class="bmd-label-floating required">{{
                      $t('cruds.income.fields.amount')
                    }}</label>
                    <input
                      class="form-control"
                      type="number"
                      step="0.01"
                      :value="entry.amount"
                      @input="updateAmount"
                      @focus="focusField('amount')"
                      @blur="clearFocus"
                      required
                    />
                  </div>
                  <div
                    class="form-group bmd-form-group"
                    :class="{
                      'has-items': entry.description,
                      'is-focused': activeField == 'description'
                    }"
                  >
                    <label class="bmd-label-floating">{{
                      $t('cruds.income.fields.description')
                    }}</label>
                    <input
                      class="form-control"
                      type="text"
                      :value="entry.description"
                      @input="updateDescription"
                      @focus="focusField('description')"
                      @blur="clearFocus"
                    />
                  </div>
                  <div class="form-group form-check">
                    <label class="form-check-label"
                      ><input
                        class="form-check-input"
                        type="checkbox"
                        :value="entry.approved"
                        :checked="entry.approved"
                        @change="updateApproved"
                      /><span class="form-check-sign"
                        ><span class="check"></span></span
                      >{{ $t('cruds.income.fields.approved') }}</label
                    >
                  </div>
                  <div class="form-group">
                    <label class="required">{{
                      $t('cruds.income.fields.document')
                    }}</label>
                    <attachment
                      :route="getRoute('incomes')"
                      :collection-name="'income_document'"
                      :media="entry.document"
                      :max-file-size="5"
                      :component="'pictures'"
                      :accept="'image/*'"
                      @file-uploaded="insertDocumentFile"
                      @file-removed="removeDocumentFile"
                      :max-files="1"
                    />
                  </div>
                </div>
              </div>
            </div>
            <div class="card-footer">
              <vue-button-spinner
                class="btn-primary"
                :status="status"
                :isLoading="loading"
                :disabled="loading"
              >
                {{ $t('global.save') }}
              </vue-button-spinner>
            </div>
          </div>
        </div>
      </div>
    </form>
  </div>
</template>

<script>
import { mapGetters, mapActions } from 'vuex'
import Attachment from '@components/Attachments/Attachment'

export default {
  components: {
    Attachment
  },
  data() {
    return {
      status: '',
      activeField: ''
    }
  },
  computed: {
    ...mapGetters('IncomesSingle', ['entry', 'loading', 'lists'])
  },
  mounted() {
    this.fetchCreateData()
  },
  beforeDestroy() {
    this.resetState()
  },
  methods: {
    ...mapActions('IncomesSingle', [
      'storeData',
      'resetState',
      'setIncomeCategory',
      'setUser',
      'setEntryDate',
      'setAmount',
      'setDescription',
      'setApproved',
      'insertDocumentFile',
      'removeDocumentFile',
      'fetchCreateData'
    ]),
    updateIncomeCategory(value) {
      this.setIncomeCategory(value)
    },
    updateUser(value) {
      this.setUser(value)
    },
    updateEntryDate(e) {
      this.setEntryDate(e.target.value)
    },
    updateAmount(e) {
      this.setAmount(e.target.value)
    },
    updateDescription(e) {
      this.setDescription(e.target.value)
    },
    updateApproved(e) {
      this.setApproved(e.target.checked)
    },
    getRoute(name) {
      return `${axios.defaults.baseURL}${name}/media`
    },
    submitForm() {
      this.storeData()
        .then(() => {
          this.$router.push({ name: 'incomes.index' })
          this.$eventHub.$emit('create-success')
        })
        .catch(error => {
          this.status = 'failed'
          _.delay(() => {
            this.status = ''
          }, 3000)
        })
    },
    focusField(name) {
      this.activeField = name
    },
    clearFocus() {
      this.activeField = ''
    }
  }
}
</script>
