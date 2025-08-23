<template>
  <v-container class="py-8" max-width="600px">
    <v-card>
      <v-card-title class="text-h6">Contact</v-card-title>
      <v-card-text>
        <v-form @submit.prevent="submit">
          <v-text-field v-model="subject" label="Sujet" prepend-inner-icon="mdi-subject" required />
          <v-textarea v-model="message" label="Message" auto-grow prepend-inner-icon="mdi-message-text" required />
          <v-file-input label="Pièce jointe" v-model="file" show-size prepend-icon="mdi-paperclip"
            accept=".pdf,.jpg,.png,.doc,.docx" />

          <v-alert v-model="tooBigAlert" type="error" class="mt-2" closable>
            Le fichier dépasse 2 Mo.
          </v-alert>

          <v-alert v-model="errorAlert" type="error" class="mt-2" closable>
            {{ errorMessage }}
          </v-alert>

          <div class="d-flex justify-end mt-4">
            <v-btn type="submit" color="primary" :loading="sending" :disabled="sending || tooBig">
              Envoyer
            </v-btn>
          </div>
        </v-form>
      </v-card-text>
    </v-card>

    <v-snackbar v-model="snackbar" :color="snackbarColor" timeout="4000">
      {{ snackbarText }}
    </v-snackbar>
  </v-container>
</template>

<script setup lang="ts">
import { ref, onMounted, computed, watch } from 'vue'

const subject = ref('')
const message = ref('')
const file = ref<File | null>(null)

const sending = ref(false)
const retryAfter = ref<string | undefined>()
const errorMessage = ref('')
const tooBigAlert = ref(false)
const errorAlert = ref(false)

const snackbar = ref(false)
const snackbarText = ref('')
const snackbarColor = ref<'success' | 'error' | 'warning'>('success')

const tooBig = computed(() => file.value ? file.value.size > 2_000_000 : false)
watch(tooBig, v => tooBigAlert.value = v)
watch(errorMessage, v => errorAlert.value = !!v)

const refreshQuota = async () => {
  await fetch('/api/contact/can-send', { credentials: 'include' })
}

const submit = async () => {
  if (tooBig.value) return
  sending.value = true
  errorMessage.value = ''

  const fd = new FormData()
  fd.set('subject', subject.value)
  fd.set('message', message.value)
  if (file.value) fd.set('attachment', file.value)

  try {
    const res = await fetch('/api/contact', { method: 'POST', body: fd, credentials: 'include' })
    if (res.ok) {
      snackbarText.value = 'Message envoyé avec succès'
      snackbarColor.value = 'success'
      snackbar.value = true

      subject.value = ''
      message.value = ''
      file.value = null
    } else if (res.status === 429) {
      const data = await res.json()
      errorMessage.value = data.error || 'Trop de messages. Réessayez plus tard.'
      retryAfter.value = data.retryAfter
    } else {
      snackbarText.value = 'Erreur lors de l’envoi'
      snackbarColor.value = 'error'
      snackbar.value = true
    }
  } catch (e) {
    snackbarText.value = 'Impossible de contacter le serveur'
    snackbarColor.value = 'error'
    snackbar.value = true
  } finally {
    sending.value = false
    await refreshQuota()
  }
}
onMounted(refreshQuota)
</script>
