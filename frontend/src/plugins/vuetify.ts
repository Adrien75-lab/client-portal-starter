// plugins/vuetify.ts
import 'vuetify/styles'
import { createVuetify } from 'vuetify'
import * as components from 'vuetify/components'
import * as directives from 'vuetify/directives'
import { fr } from 'vuetify/locale'

export const vuetify = createVuetify({
  components,
  directives,
  locale: {
    locale: 'fr',
    messages: { fr },
  },
})