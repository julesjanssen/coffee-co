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
          <span v-if="data.debug" class="badge danger">on</span>
          <span v-else class="badge">off</span>
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
          <span v-if="!data.secureCookie" class="badge warning">not secure</span>
          <span v-else class="badge">secure</span>
        </dd>
      </div>

      <div>
        <dt>Config cache</dt>
        <dd>
          <span v-if="!data.cachedConfig" class="badge warning">not cached</span>
          <span v-else class="badge success">cached</span>
        </dd>
      </div>

      <div>
        <dt>Routes cache</dt>
        <dd>
          <span v-if="!data.cachedRoutes" class="badge warning">not cached</span>
          <span v-else class="badge success">cached</span>
        </dd>
      </div>

      <div>
        <dt>Events cache</dt>
        <dd>
          <span v-if="!data.cachedEvents" class="badge warning">not cached</span>
          <span v-else class="badge success">cached</span>
        </dd>
      </div>

      <div v-if="data.path">
        <dt>PATH setting</dt>
        <dd class="path">
          <span v-if="!data.path.length" class="badge danger">PATH not set</span>
          <details v-else>
            <summary>
              <span class="badge success">PATH set</span>
            </summary>
            <ol>
              <li v-for="(element, index) in data.path" :key="index">
                <code>{{ element }}</code>
              </li>
            </ol>
          </details>
        </dd>
      </div>

      <div>
        <dt>Composer dev</dt>
        <dd>
          <span v-if="data.composerDev" class="badge danger">dev packages installed</span>
          <span v-else class="badge success">no dev</span>
        </dd>
      </div>

      <div>
        <dt>Backup encryption</dt>
        <dd>
          <span v-if="data.backupEncryption" class="badge success">encrypted</span>
          <span v-else class="badge danger">not encrypted</span>
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

  & ol {
    width: fit-content;
    min-width: 20em;
    padding: 0.45em 0;
    padding-left: 2em;
    border-radius: 3px;
    background: var(--gray-800);
    font-size: 0.9em;
    list-style: decimal;

    li {
      padding: 0.35em 0.5em;
      color: var(--gray-500);
      line-height: 1.2;

      & code {
        color: var(--gray-200);
      }

      &:first-child code {
        color: var(--green-400);
      }
    }
  }
}
</style>
