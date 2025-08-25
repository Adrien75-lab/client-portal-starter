<template>
  <v-container class="d-flex align-center justify-center" style="height: 100vh">
    <v-card max-width="400" class="pa-6" elevation="6">
      <v-card-title class="text-h6 justify-center">Connexion</v-card-title>
      <v-card-text>
        <v-form @submit.prevent="submit">
          <v-text-field v-model="email" label="Email" type="email" prepend-inner-icon="mdi-email" required />

          <v-text-field v-model="password" label="Mot de passe" type="password" prepend-inner-icon="mdi-lock"
            required />

          <v-alert v-if="err" type="error" class="mt-2" density="compact" closable>
            {{ err }}
          </v-alert>

          <div class="d-flex justify-center mt-4">
            <v-btn type="submit" color="primary" :loading="loading" :disabled="loading" rounded="lg">
              Se connecter
            </v-btn>
          </div>
        </v-form>
      </v-card-text>
    </v-card>
    <v-snackbar v-model="snackbar" color="success" timeout="3000" location="top right">
      ✅ Connexion réussie
    </v-snackbar>
  </v-container>
</template>

<script setup lang="ts">
import { ref } from "vue";
import { useRouter } from "vue-router";
import { useAuth } from "../stores/auth";

const router = useRouter();
const auth = useAuth();

const email = ref("");
const password = ref("");
const loading = ref(false);
const err = ref("");
const snackbar = ref(false);

const submit = async () => {
  err.value = "";
  loading.value = true;
  try {
    await auth.login(email.value, password.value)
    snackbar.value = true
    console.log("Login successful, redirecting to /contact...");
    setTimeout(() => {
      router.push('/contact')
    }, 1500)
  } catch {
    err.value = "Identifiants invalides";
  } finally {
    loading.value = false;
  }
};
</script>
