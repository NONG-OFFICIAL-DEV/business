<script setup>
  defineProps({
    isHospitality: Boolean,
    table: Object,
    count: Number
  })

  defineEmits(['clear'])
</script>

<template>
  <div
    class="px-4 py-3 border-b d-flex align-center justify-space-between"
    :class="isHospitality ? 'bg-white' : 'bg-primary text-white'"
  >
    <div>
      <div class="text-subtitle-2 font-weight-black">
        {{ isHospitality ? 'CURRENT ORDER' : 'CART' }}
      </div>

      <div v-if="isHospitality && table" class="text-caption">
        Table {{ table.number }}
      </div>

      <div v-else class="text-caption">Items Count {{ count }}</div>
    </div>

    <v-btn
      icon="mdi-delete-sweep"
      variant="tonal"
      size="x-small"
      color="error"
      @click="$emit('clear')"
    />
  </div>
</template>
