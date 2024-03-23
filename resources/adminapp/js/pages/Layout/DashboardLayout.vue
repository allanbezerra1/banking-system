<template>
  <div class="wrapper" :class="{ 'nav-open': $sidebar.showSidebar }">
    <event-hub></event-hub>
    <side-bar :sidebarLinks="sidebarLinks">
      <mobile-menu slot="content"></mobile-menu>
    </side-bar>

    <div class="main-panel">
      <top-navbar></top-navbar>
      <div class="content">
        <dashboard-content></dashboard-content>
      </div>
    </div>
  </div>
</template>

<script>
import DashboardContent from './Content.vue'
import TopNavbar from './TopNavbar.vue'
import MobileMenu from './MobileMenu.vue'

export default {
  components: {
    DashboardContent,
    TopNavbar,
    MobileMenu
  },
  data() {
    return {
      sidebarLinks: [
        {
          title: 'global.dashboard',
          icon: 'dashboard',
          path: { name: 'dashboard' }
        },
        {
          title: 'cruds.userManagement.title',
          icon: 'person',
          path: { name: 'user_management' },
          gate: 'user_management_access',
          children: [
            {
              title: 'cruds.permission.title',
              icon: 'perm_data_setting',
              path: { name: 'permissions.index' },
              gate: 'permission_access'
            },
            {
              title: 'cruds.role.title',
              icon: 'group',
              path: { name: 'roles.index' },
              gate: 'role_access'
            },
            {
              title: 'cruds.user.title',
              icon: 'person',
              path: { name: 'users.index' },
              gate: 'user_access'
            }
          ]
        },
        {
          title: 'cruds.expenseManagement.title',
          icon: 'account_balance_wallet',
          path: { name: 'expense_management' },
          gate: 'expense_management_access',
          children: [
            {
              title: 'cruds.expenseCategory.title',
              icon: 'table_view',
              path: { name: 'expense_categories.index' },
              gate: 'expense_category_access'
            },
            {
              title: 'cruds.incomeCategory.title',
              icon: 'table_view',
              path: { name: 'income_categories.index' },
              gate: 'income_category_access'
            },
            {
              title: 'cruds.expense.title',
              icon: 'table_view',
              path: { name: 'expenses.index' },
              gate: 'expense_access'
            },
            {
              title: 'cruds.income.title',
              icon: 'table_view',
              path: { name: 'incomes.index' },
              gate: 'income_access'
            },
            {
              title: 'cruds.expenseReport.title',
              icon: 'table_view',
              path: { name: 'expense_reports.index' },
              gate: 'expense_report_access'
            }
          ]
        }
      ]
    }
  }
}
</script>
