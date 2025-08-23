<template>
    <v-container>
        <v-card>
            <v-card-title class="text-h6">Mes fichiers envoyés</v-card-title>
            <v-data-table :headers="headers" :items="files" :loading="loading" class="elevation-1"
                :items-per-page-text="'Éléments par page'" :locale="'fr'">
                <template #no-data>
                    <div class="text-center q-pa-md">
                        📂 Aucun fichier envoyé pour le moment.
                    </div>
                </template>
                <!-- Colonne Nom du fichier -->
                <template #item.filename="{ item }">
                    <span>{{ item.attachment }}</span>
                </template>

                <!-- Colonne Fichier (Aperçu + Download) -->
                <template #item.attachment="{ item }">
                    <div class="d-flex align-center">
                        <v-btn v-if="item.attachment" icon="mdi-eye" variant="text" @click="previewFile(item)" />
                        <v-btn v-if="item.attachment" :href="`/uploads/${item.attachment}`" target="_blank"
                            icon="mdi-download" variant="text" />
                    </div>
                </template>

                <!-- Bouton corbeille -->
                <template #item.actions="{ item }">
                    <v-btn color="error" icon="mdi-delete" variant="text" @click="deleteFile(item.id)" />
                </template>
                <template #footer.page-text="{ pageStart, pageStop, itemsLength }">
                    {{ pageStart }}–{{ pageStop }} sur {{ itemsLength }}
                </template>
            </v-data-table>

        </v-card>

        <!-- Dialog aperçu -->
        <v-dialog v-model="previewDialog" max-width="800px">
            <v-card>
                <v-card-title class="text-h6">Aperçu du fichier</v-card-title>
                <v-card-text>
                    <div v-if="previewFileUrl && previewFileUrl.endsWith('.pdf')">
                        <iframe :src="previewFileUrl" width="100%" height="600px"></iframe>
                    </div>
                    <div v-else-if="previewFileUrl">
                        <img :src="previewFileUrl" style="max-width: 100%; max-height: 600px;" />
                    </div>
                    <div v-else>
                        Aucun aperçu disponible
                    </div>
                </v-card-text>
                <v-card-actions>
                    <v-spacer />
                    <v-btn text @click="previewDialog = false">Fermer</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </v-container>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'

const files = ref<any[]>([])
const loading = ref(true)

const previewDialog = ref(false)
const previewFileUrl = ref<string | null>(null)

const headers = [
    { title: 'Sujet', key: 'subject' },
    { title: 'Message', key: 'message' },
    { title: 'Nom du fichier', key: 'filename' }, // <-- nouvelle colonne
    { title: 'Fichier', key: 'attachment' },
    { title: 'Date', key: 'createdAt' },
    { title: 'Actions', key: 'actions', sortable: false },
]

onMounted(async () => {
    await loadFiles()
})

async function loadFiles() {
    loading.value = true
    try {
        const res = await fetch('/api/my-files', { credentials: 'include' })
        if (res.ok) {
            files.value = await res.json()
        }
    } finally {
        loading.value = false
    }
}

function previewFile(item: any) {
    previewFileUrl.value = `/uploads/${item.attachment}`
    previewDialog.value = true
}

async function deleteFile(id: number) {
    if (!confirm('Supprimer ce fichier ?')) return
    const res = await fetch(`/api/my-files/${id}`, {
        method: 'DELETE',
        credentials: 'include',
    })
    if (res.ok) {
        files.value = files.value.filter(f => f.id !== id)
    }
}
</script>
