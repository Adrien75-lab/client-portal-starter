<template>
  <v-container class="d-flex align-center justify-center" style="height: 100vh">
    <v-card max-width="400" class="pa-6" elevation="6">
      <v-card-title class="text-h6 justify-center">Inscription</v-card-title>
      <v-card-text>
        <v-form @submit.prevent="submit">
          <v-text-field v-model="email" label="Email" type="email" prepend-inner-icon="mdi-email" required />
          <v-text-field v-model="password" :type="showPassword ? 'text' : 'password'" label="Mot de passe"
            prepend-inner-icon="mdi-lock" :append-inner-icon="showPassword ? 'mdi-eye-off' : 'mdi-eye'"
            @click:append-inner="showPassword = !showPassword" :error="!passwordValid && password.length > 0"
            :error-messages="passwordErrorMessage" required />
          <v-list density="compact" class="mt-2">
            <v-list-item :class="{ 'text-success': /[A-Z]/.test(password) }">
              <v-list-item-title>1 majuscule</v-list-item-title>
            </v-list-item>
            <v-list-item :class="{ 'text-success': /[0-9]/.test(password) }">
              <v-list-item-title>1 chiffre</v-list-item-title>
            </v-list-item>
            <v-list-item :class="{ 'text-success': /[^A-Za-z0-9]/.test(password) }">
              <v-list-item-title>1 caractère spécial</v-list-item-title>
            </v-list-item>
            <v-list-item :class="{ 'text-success': password.length >= 8 }">
              <v-list-item-title>≥ 8 caractères</v-list-item-title>
            </v-list-item>
          </v-list>

          <div class="d-flex justify-center mt-4">
            <v-btn type="submit" color="primary" :loading="loading" :disabled="loading" rounded="lg">
              Créer le compte
            </v-btn>
          </div>
        </v-form>
      </v-card-text>
    </v-card>
    <v-snackbar v-model="snackbarSuccess" color="success" timeout="3000" location="top right">
      ✅ Compte créé avec succès
    </v-snackbar>
    <v-snackbar v-model="snackbarError" color="error" timeout="3000" location="top right">
      ❌ Erreur lors de l'inscription
    </v-snackbar>
  </v-container>
</template>

<script setup lang="ts">
import { ref, computed } from "vue"

const email = ref("")
const password = ref("")
const loading = ref(false)
const showPassword = ref(false)
const snackbarSuccess = ref(false)
const snackbarError = ref(false)
const passwordValid = computed(() => {
  return (
    /[A-Z]/.test(password.value) &&
    /[0-9]/.test(password.value) &&
    /[^A-Za-z0-9]/.test(password.value) &&
    password.value.length >= 8
  )
})

const passwordErrorMessage = computed(() => {
  if (password.value.length === 0) return ""
  if (passwordValid.value) return ""
  return "Le mot de passe doit contenir 1 majuscule, 1 chiffre, 1 spécial et ≥ 8 caractères"
})

const submit = async () => {
  loading.value = true
  try {
    const res = await fetch("/auth/register", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ email: email.value, password: password.value }),
    })
    if (res.ok) {
      snackbarSuccess.value = true
      email.value = ""
      password.value = ""
    } else {
      snackbarError.value = true
    }
  } catch {
    snackbarError.value = true
  } finally {
    loading.value = false
  }
}
</script>
