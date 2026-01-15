<template>
  <v-dialog v-model="modelValue" max-width="500">
    <v-card>
      <v-card-title>
        {{ form.id ? "Edit Employee" : "Add Employee" }}
      </v-card-title>

      <v-card-text>
        <v-text-field v-model="form.name" label="Name" />
        <v-text-field v-model="form.email" label="Email" />
        <v-text-field v-model="form.position" label="Position" />
        <v-text-field
          v-model="form.salary"
          label="Salary"
          type="number"
        />
      </v-card-text>

      <v-card-actions>
        <v-spacer />
        <v-btn text @click="close">Cancel</v-btn>
        <v-btn color="primary" @click="submit">Save</v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script setup>
import { reactive, watch } from "vue";

const props = defineProps({
  modelValue: Boolean,
  employee: Object,
});

const emit = defineEmits(["update:modelValue", "save"]);

const form = reactive({
  id: null,
  name: "",
  email: "",
  position: "",
  salary: "",
});

watch(
  () => props.employee,
  (val) => {
    if (val) Object.assign(form, val);
    else reset();
  },
  { immediate: true }
);

function reset() {
  Object.assign(form, {
    id: null,
    name: "",
    email: "",
    position: "",
    salary: "",
  });
}

function close() {
  emit("update:modelValue", false);
  reset();
}

function submit() {
  emit("save", { ...form });
  close();
}
</script>
