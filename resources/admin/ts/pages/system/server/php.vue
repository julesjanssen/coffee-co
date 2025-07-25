<template>
  <section>
    <header>
      <div>
        <h1>PHP</h1>
      </div>
    </header>

    <dl>
      <div v-if="data.releaseInfo">
        <dt>PHP version</dt>
        <dd>
          <details :open="isPast(data.releaseInfo.activeUntil)">
            <summary>{{ data.version }}</summary>
            <table class="version-details">
              <tbody>
                <tr>
                  <th>active support:</th>
                  <td>
                    <span class="badge" :class="{ warning: isPast(data.releaseInfo.activeUntil) }">
                      <DateTime :datetime="data.releaseInfo.activeUntil" />
                    </span>
                  </td>
                </tr>
                <tr>
                  <th>security support:</th>
                  <td>
                    <span class="badge" :class="{ danger: isPast(data.releaseInfo.securityUntil) }">
                      <DateTime :datetime="data.releaseInfo.securityUntil" />
                    </span>
                  </td>
                </tr>
                <tr>
                  <th>patch available:</th>
                  <td>
                    <span class="badge" :class="{ warning: data.releaseInfo.patchAvailable }">
                      {{ data.releaseInfo.patchAvailable ? 'yes' : 'no ' }}
                    </span>
                  </td>
                </tr>
              </tbody>
            </table>
          </details>
        </dd>
      </div>
      <div v-else>
        <dt>PHP version</dt>
        <dd>{{ data.version }}</dd>
      </div>

      <div>
        <dt>OPcache</dt>
        <dd>
          <span v-if="data.opcache === false" class="badge danger">disabled</span>
          <template v-else>
            <span class="badge success">enabled</span> (using {{ filesize(data.opcache.usedMemory) }})
            <button
              v-if="data.opcache.usedMemory > 0"
              type="button"
              class="small inline"
              style="margin-left: 1em"
              @click.prevent="resetOpcache"
            >
              reset
            </button>
          </template>
        </dd>
      </div>

      <div>
        <dt>Max upload size:</dt>
        <dd>{{ filesize(data.maxUpload) }}</dd>
      </div>

      <div>
        <dt>Max execution time:</dt>
        <dd>{{ data.maxExecution }} seconds</dd>
      </div>

      <div>
        <dt>Memory limit:</dt>
        <dd>{{ filesize(data.memoryLimit) }}</dd>
      </div>
    </dl>
  </section>
</template>

<script lang="ts" setup>
import { router } from '@inertiajs/vue3'
import filesize from 'filesize.js'

import DateTime from '/@admin:components/DateTime.vue'
import { http } from '/@admin:shared/http'

defineProps<{
  data: any
}>()

const now = new Date()

const isPast = (value: string) => {
  const date = new Date(value)

  return date.getTime() < now.getTime()
}

const resetOpcache = () => {
  http
    .post(location.pathname, {
      action: 'resetOpcache',
    })
    .then(() => {
      router.reload()
    })
}
</script>
