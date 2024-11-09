<template>
  <section>
    <header>
      <div>
        <h1>Configuration</h1>
      </div>
    </header>

    <dl>
      <div>
        <dt>Debug mode</dt>
        <dd>
          <span class="badge danger" v-if="data.debug">on</span>
          <span class="badge" v-else>off</span>
        </dd>
      </div>

      <div v-if="data.scheme">
        <dt>Scheme</dt>
        <dd>
          <span v-if="!data.scheme.match" class="badge danger">
            configured app.url does not match current scheme: {{ data.scheme.current }}
          </span>
          <span v-else class="badge" :class="{ warning: data.scheme.configured === 'http' }">
            {{ data.scheme.configured }}
          </span>
        </dd>
      </div>

      <div>
        <dt>Cookie</dt>
        <dd>
          <span class="badge warning" v-if="!data.secureCookie">not secure</span>
          <span class="badge" v-else>secure</span>
        </dd>
      </div>

      <div>
        <dt>Config cache</dt>
        <dd>
          <span class="badge warning" v-if="!data.cachedConfig">not cached</span>
          <span class="badge success" v-else>cached</span>
        </dd>
      </div>

      <div>
        <dt>Routes cache</dt>
        <dd>
          <span class="badge warning" v-if="!data.cachedRoutes">not cached</span>
          <span class="badge success" v-else>cached</span>
        </dd>
      </div>

      <div>
        <dt>Events cache</dt>
        <dd>
          <span class="badge warning" v-if="!data.cachedEvents">not cached</span>
          <span class="badge success" v-else>cached</span>
        </dd>
      </div>

      <div v-if="data.path">
        <dt>PATH setting</dt>
        <dd class="path">
          <span class="badge danger" v-if="!data.path.length">PATH not set</span>
          <details v-else>
            <summary>
              <span class="badge success">PATH set</span>
            </summary>
            <ul>
              <li v-for="(element, index) in data.path" :key="index">
                <code>{{ element }}</code>
              </li>
            </ul>
          </details>
        </dd>
      </div>

      <div>
        <dt>Composer dev</dt>
        <dd>
          <span class="badge danger" v-if="data.composerDev">dev packages installed</span>
          <span class="badge success" v-else>no dev</span>
        </dd>
      </div>

      <div>
        <dt>Backup encryption</dt>
        <dd>
          <span class="badge success" v-if="data.backupEncryption">encrypted</span>
          <span class="badge danger" v-else>not encrypted</span>
        </dd>
      </div>
    </dl>
  </section>
</template>

<script lang="ts" setup>
defineProps<{
  data: any
}>()
</script>

<style scoped>
dd.path {
  & .badge {
    font-weight: normal;
  }

  & ul li {
    & code {
      display: inline-block;
      padding: 0.35em 0.5em;
      border-radius: 3px;
      background: var(--gray-800);
      color: var(--green-400);
      font-size: 0.9em;
      line-height: 1;
    }

    & + li {
      margin-top: 0.25em;
    }
  }
}
</style>
