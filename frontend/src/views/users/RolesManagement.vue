<script setup>
  import { ref, onMounted } from 'vue'
  import { useRoleStore } from '@/stores/roleStore'
  import { useAppUtils } from '@/composables/useAppUtils'
  import { useI18n } from 'vue-i18n'
  import RoleDialog from '@/components/users/RoleDialog.vue'

  const dialog = ref(false)
  const selectedRole = ref({})
  const { t } = useI18n()

  const { confirm, notif } = useAppUtils()
  // Pinia store
  const rolesStore = useRoleStore()

  // Fetch roles on mount
  onMounted(() => {
    rolesStore.fetchRoles()
  })

  // Open add dialog
  function openAdd() {
    selectedRole.value = {}
    dialog.value = true
  }

  // Open update dialog
  function openEdit(role) {
    selectedRole.value = { ...role }
    dialog.value = true
  }
  function onDeleteRole(role) {
    confirm({
      title: 'Are you sure?',
      message: 'Are you sure you want to delete this role?',
      options: {
        type: 'error',
        color: 'error',
        width: 400
      },
      agree: async () => {
        await rolesStore.deleteRole(role)
        notif(t('messages.deleted_success'), {
          type: 'success',
          color: 'primary'
        })
      }
    })
  }

  // Handle submit
  async function saveRole(role) {
    if (role.id) {
      await rolesStore.updateRole(role)
    } else {
      await rolesStore.addRole(role)
    }
    await rolesStore.fetchRoles()
    dialog.value = false
  }
</script>

<template>
  <v-container fluid class="pa-0">
    <custom-title icon="mdi-account-group" i>
      System Roles
      <template #right>
        <v-btn
          prepend-icon="mdi-plus"
          color="primary"
          size="large"
          @click="openAdd"
        >
          Create New Role
        </v-btn>
      </template>
    </custom-title>

    <v-row>
      <v-col
        v-for="role in rolesStore.roles.data"
        :key="role.id"
        cols="12"
        md="6"
        lg="4"
      >
        <v-card
          class="role-card rounded-xl border-0 overflow-hidden"
          elevation="0"
        >
          <div
            :class="[
              'status-stripe',
              role.status === 1 ? 'bg-success' : 'bg-error'
            ]"
          ></div>

          <v-card-text class="pa-5">
            <div class="d-flex justify-space-between align-start mb-4">
              <div>
                <h3 class="text-h5 font-weight-bold text-grey-darken-4">
                  {{ role.name }}
                </h3>
                <div class="d-flex align-center mt-1">
                  <v-icon size="16" color="grey" class="me-1">
                    mdi-account-group-outline
                  </v-icon>
                  <span class="text-caption text-grey font-weight-medium">
                    12 Members assigned
                  </span>
                </div>
              </div>

              <v-btn
                variant="tonal"
                :color="role.status === 1 ? 'success' : 'error'"
                size="small"
                class="rounded-lg font-weight-bold"
                readonly
              >
                {{ role.status === 1 ? 'Active' : 'Disabled' }}
              </v-btn>
            </div>

            <p class="text-body-2 text-grey-darken-1 mb-6 role-description">
              {{
                role.description ||
                'Manage core system settings and user access levels.'
              }}
            </p>

            <div class="mb-4">
              <div
                class="text-overline font-weight-black text-grey-lighten-1 mb-2"
              >
                Top Permissions
              </div>
              <div class="d-flex flex-wrap gap-2">
                <v-chip
                  size="x-small"
                  variant="flat"
                  color="grey-lighten-3"
                  class="rounded-lg"
                >
                  Dashboard
                </v-chip>
                <v-chip
                  size="x-small"
                  variant="flat"
                  color="grey-lighten-3"
                  class="rounded-lg"
                >
                  Sales
                </v-chip>
                <v-chip
                  size="x-small"
                  variant="flat"
                  color="grey-lighten-3"
                  class="rounded-lg"
                >
                  +4 more
                </v-chip>
              </div>
            </div>
          </v-card-text>

          <v-divider class="border-opacity-25" />

          <v-card-actions class="pa-4 bg-grey-lighten-5">
            <v-btn
              variant="text"
              color="error"
              class="rounded-lg text-none font-weight-bold"
              @click="onDeleteRole(role.id)"
            >
              <v-icon start>mdi-trash-can-outline</v-icon>
              Remove
            </v-btn>
            <v-spacer />
            <v-btn
              variant="flat"
              color="white"
              border
              class="rounded-lg text-none font-weight-bold px-4"
              @click="openEdit(role)"
            >
              <v-icon start>mdi-pencil-outline</v-icon>
              Edit Role
            </v-btn>
          </v-card-actions>
        </v-card>
      </v-col>
    </v-row>

    <RoleDialog v-model="dialog" :editedRole="selectedRole" @save="saveRole" />
  </v-container>
</template>

<style scoped>
  .role-card {
    position: relative;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    background: white;
    border: 1px solid #edf2f7 !important;
  }

  .role-card:hover {
    transform: translateY(-8px);
    box-shadow:
      0 20px 25px -5px rgba(0, 0, 0, 0.05),
      0 10px 10px -5px rgba(0, 0, 0, 0.02) !important;
  }

  .status-stripe {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
  }

  .role-description {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    height: 40px;
  }

  .gap-2 {
    gap: 8px;
  }
</style>
