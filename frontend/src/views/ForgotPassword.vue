<template>
  <div>
    <h1>Mot de passe oublié</h1>
    <form @submit.prevent="submit">
      <input v-model="email" type="email" placeholder="Email" required />
      <button>Envoyer le lien</button>
    </form>
  </div>
</template>
<script setup lang="ts">
import { ref } from 'vue'
const email = ref('')
const submit = async () => {
  await fetch('/auth/forgot-password',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({email:email.value})})
}
</script>
