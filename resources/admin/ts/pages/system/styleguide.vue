<template>
  <Head title="Styleguide" />

  <main class="styleguide">
    <section>
      <header>
        <div>
          <a href="#" class="back"> back </a>

          <h1>Definitie lijst</h1>
          <h2>step 1</h2>
        </div>

        <div class="actions">
          <button type="button" class="danger" v-on:click.prevent="deleteItem" v-close-popper>
            <Icon name="trash" />
            {{ $t('remove') }}
          </button>
        </div>
      </header>

      <dl>
        <div>
          <dt>complex nr</dt>
          <dd>01236</dd>
        </div>
        <div>
          <dt>BAG ID</dt>
          <dd>
            <a
              href="https://bagviewer.kadaster.nl/lvbag/bag-viewer/index.html#?searchQuery=0935100000022691"
              target="_blank"
              rel="noopener noreferrer"
              class="external"
              >0935100000022691</a
            >
          </dd>
        </div>
        <div>
          <dt>categorie</dt>
          <dd><span class="badge">woonruimte</span></dd>
        </div>
        <div>
          <dt>adres</dt>
          <dd>
            Sint Maartenspoort 13<br />
            6221BA Maastricht
          </dd>
        </div>
        <div>
          <dt>eigenaar</dt>
          <dd>-</dd>
        </div>
        <div>
          <dt>beheerder</dt>
          <dd>Vagebond International Holdings Inc. Ltd. S.A. GmbH.</dd>
        </div>
        <div>
          <dt>badges</dt>
          <dd>
            Lorem ipsum <span class="badge success">lorem ipsum dolor</span> <span class="badge danger">sit amet</span>
          </dd>
        </div>
        <div>
          <dt>item met inline button</dt>
          <dd>
            The label &nbsp;
            <a href="#" class="button small">the button</a>
          </dd>
        </div>
        <div>
          <dt>details</dt>
          <dd>
            <details>
              <summary>8.3.11 (fpm-fcgi)</summary>

              <table class="version-details">
                <tbody>
                  <tr>
                    <th>active support:</th>
                    <td>
                      <span class="badge"><DateTime datetime="2024-05-12" /></span>
                    </td>
                  </tr>
                  <tr>
                    <th>security support:</th>
                    <td>
                      <span class="badge"><DateTime datetime="now" /></span>
                    </td>
                  </tr>
                  <tr>
                    <th>patch available:</th>
                    <td><span class="badge warning">yes</span></td>
                  </tr>
                </tbody>
              </table>
            </details>
          </dd>
        </div>
      </dl>

      <h2>Tussenkop</h2>
      <dl>
        <div>
          <dt>klant</dt>
          <dd>Tom Huynen</dd>
        </div>
        <div>
          <dt>Intern contactpersoon</dt>
          <dd>Gianluca Riggio</dd>
        </div>
        <div>
          <dt>Functie</dt>
          <dd>Chef Glimmende Dingetjes (adjunct)</dd>
        </div>
      </dl>
    </section>

    <section>
      <header>
        <div>
          <h1>Formulier</h1>
          <h2>stap 2</h2>
        </div>
      </header>

      <form method="post">
        <fieldset>
          <legend>personal information</legend>

          <div class="field">
            <label>name:</label>
            <div>
              <input type="text" placeholder="name" />
            </div>
          </div>

          <div class="field">
            <label>email:</label>
            <div>
              <input type="email" />
              <FormError error="This email address is already in use" />
            </div>
          </div>

          <div class="field">
            <label>Prefix</label>
            <div data-prefix="%">
              <input type="text" />
            </div>
          </div>

          <div class="field">
            <label>Suffix</label>
            <div data-suffix="$">
              <input type="text" />
            </div>
          </div>

          <div class="field">
            <label>Prefixed and suffixed</label>
            <div data-suffix="$" data-prefix="%">
              <input type="text" />
            </div>
          </div>

          <div class="field">
            <label>checkboxes here:</label>
            <div>
              <label class="checkbox">
                <input type="checkbox" name="dinges" value="1" />
                <span class="label">this is the first label</span>
              </label>
              <label class="checkbox">
                <input type="checkbox" name="dinges" value="2" />
                <span class="label">this is the second label</span>
              </label>
              <label class="checkbox">
                <input type="checkbox" name="dinges" value="3" />
                <span class="label">this is the third label<br />including a newline</span>
              </label>
              <label class="checkbox">
                <input type="checkbox" name="dinges" value="4" disabled />
                <span class="label">and a disabled checkbox</span>
              </label>
            </div>
          </div>
        </fieldset>

        <fieldset>
          <legend>more input please…</legend>

          <div class="field">
            <label>this is a select</label>
            <div>
              <select name="dinges">
                <option value>first option</option>
                <option value="2">another option</option>
                <option value="3">
                  an option with a really long piece of text in the option option dinges shizzle
                </option>
              </select>
            </div>
          </div>

          <div class="field-group">
            <div class="field">
              <label>textarea?</label>
              <div>
                <textarea rows="3" cols="40"></textarea>
                <p class="help">A piece of helptext for this input.</p>
              </div>
            </div>

            <div class="field">
              <label>textarea autosizing</label>
              <div>
                <textarea rows="3" cols="40" class="autosize"></textarea>
              </div>
            </div>
          </div>

          <div class="field-group">
            <div class="field">
              <label>datum</label>
              <div>
                <input type="date" />
              </div>
            </div>

            <div class="field">
              <label>tijd</label>
              <div>
                <input type="time" />
              </div>
            </div>
          </div>

          <div class="field-group">
            <div class="field">
              <label>email:</label>
              <div>
                <input type="email" />
                <p class="help">
                  Dit is ook handig om te weten over
                  <strong>email</strong>
                </p>
              </div>
            </div>

            <div class="field">
              <label>wachtwoord</label>
              <div>
                <input type="password" value="hunter2" />
              </div>
            </div>
          </div>

          <div class="field-group">
            <div class="field">
              <label>disabled email:</label>
              <div>
                <input type="email" name="email[]" disabled value="test@example.org" />
                <p class="help">
                  Dit is ook handig om te weten over
                  <strong>email</strong>
                </p>
              </div>
            </div>
            <div class="field">
              <label>readonly email:</label>
              <div>
                <input type="email" name="email[]" readonly value="test@example.net" />
                <p class="help">
                  Dit is ook handig om te weten over
                  <strong>email</strong>
                </p>
              </div>
            </div>
          </div>

          <div class="field">
            <label>je moet kiezen:</label>
            <div>
              <label class="radio">
                <input type="radio" name="radio" value="1" />
                <span class="label">this is the first label</span>
              </label>
              <label class="radio">
                <input type="radio" name="radio" value="2" />
                <span class="label">this is the second label</span>
              </label>
              <label class="radio">
                <input type="radio" name="radio" value="4" />
                <span class="label"
                  >Lorem ipsum dolor, sit amet consectetur adipisicing elit. Dignissimos magnam sapiente voluptate cum
                  non minima placeat possimus dicta fugiat unde. Quisquam, nostrum quam illum cum repellat tempore
                  atque. Assumenda, hic?</span
                >
              </label>
              <label class="radio">
                <input type="radio" disabled name="radio" value="3" />
                <span class="label">this is the second label</span>
              </label>
            </div>
          </div>

          <div class="field-group">
            <div class="field">
              <label>upload</label>
              <div>
                <FileInput />
              </div>
            </div>

            <div class="field">
              <label>images upload</label>
              <div>
                <FileInput :multiple="true" accept="image/*" />
              </div>
            </div>
          </div>
        </fieldset>

        <footer>
          <fieldset class="actions">
            <button type="button" class="success"><Icon name="plus" /> success</button>
            <button type="button" disabled class="success"><Icon name="plus" /> success disabled</button>
            <button type="button" disabled><Icon name="plus" /> regular disabled</button>
          </fieldset>
        </footer>
      </form>
    </section>

    <section>
      <header>
        <div>
          <h1>Buttons &amp; badges</h1>
          <h2>step 2</h2>
        </div>
        <div class="actions">
          <button type="button">action</button>
        </div>
      </header>

      <article>
        <ul class="badges">
          <li>
            <span class="badge">Normal</span>
          </li>
          <li>
            <span class="badge success">Success</span>
          </li>
          <li>
            <span class="badge danger">Danger</span>
          </li>
          <li>
            <span class="badge warning">Warning</span>
          </li>
          <li>
            <span class="badge primary">Primary</span>
          </li>
          <li>
            <span class="badge primary"> <Icon name="nav-database" /> database </span>
          </li>
          <li>
            <span class="badge primary icon-only">
              <Icon name="nav-database" />
            </span>
          </li>
        </ul>

        <ul class="buttons" style="margin: 1.5rem 0">
          <li>
            <button type="button" class="primary"><Icon name="nav-database" /> primary</button>
          </li>
          <li>
            <button type="button" class="success"><Icon name="globe" /> add more</button>
          </li>
          <li>
            <button type="button" class="warning">warning</button>
          </li>
          <li>
            <button type="button" class="danger">danger</button>
          </li>
          <li>
            <button type="button" class="ghost">ghost button</button>
          </li>
          <li>
            <button type="button"><Icon name="nav-database" /> regular button</button>
          </li>
        </ul>

        <ul class="buttons" style="margin: 1.5rem 0">
          <li>
            <button type="button" class="primary" disabled><Icon name="nav-database" /> primary</button>
          </li>
          <li>
            <button type="button" class="success" disabled><Icon name="globe" /> add more</button>
          </li>
          <li>
            <button type="button" class="warning" disabled>warning</button>
          </li>
          <li>
            <button type="button" class="danger" disabled>danger</button>
          </li>
          <li>
            <button type="button" class="ghost" disabled>ghost button</button>
          </li>
          <li>
            <button type="button" disabled><Icon name="nav-database" /> regular button</button>
          </li>
        </ul>
      </article>
    </section>

    <section>
      <header>
        <div>
          <h1>Table</h1>
        </div>
      </header>

      <table>
        <thead>
          <tr>
            <th>name</th>
            <th>most recent login</th>
            <th class="align-right">
              <abbr title="Two Factor Auth enabled?">2FA</abbr>
            </th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>
              <a href="#">Bram Jonkers</a>
            </td>
            <td>5 days ago</td>
            <td class="align-right">
              <span class="badge success">yes</span>
            </td>
          </tr>

          <tr>
            <td>
              <a href="#">Jules Janssen</a>
            </td>
            <td>3 seconds ago</td>
            <td class="align-right">
              <span class="badge error">no</span>
            </td>
          </tr>

          <tr class="trashed">
            <td>
              <a href="#">Karin Vrij</a>
            </td>
            <td>5 days ago</td>
            <td class="align-right">
              <span class="badge error">no</span>
            </td>
          </tr>

          <tr>
            <td>
              <a href="#">Gianluca Riggio</a>
            </td>
            <td>12 years ago</td>
            <td class="align-right">
              <span class="badge error">no</span>
            </td>
          </tr>
        </tbody>
      </table>

      <Pagination :links="{ prev: null, next: '/' }" />
    </section>

    <section>
      <header>
        <div>
          <h2>rondjes</h2>
          <h1>Progress</h1>
        </div>
      </header>

      <div style="display: flex; gap: 1em">
        <span style="color: var(--green-500)">
          <ProgressCircle :percentage="20" />
        </span>
        <span style="color: var(--blue-500)">
          <ProgressCircle :percentage="80" />
        </span>
        <span style="color: var(--red-500)">
          <ProgressCircle :percentage="45" :stroke="6" />
        </span>

        <span style="color: var(--gray-700)">
          <ProgressCircle :percentage="71" :size="20" />
        </span>

        <span style="color: var(--blue-500)">
          <ProgressCircle :percentage="37" :stroke="3" :size="20" />
        </span>

        <span style="color: var(--blue-500)">
          <ProgressIndeterminate />
        </span>
        <span style="color: var(--gray-400)">
          <ProgressIndeterminate :size="18" />
        </span>
      </div>
    </section>

    <section>
      <header>
        <div>
          <h1>Dropdowns</h1>
        </div>
      </header>

      <div style="display: flex; flex-wrap: wrap; gap: 1em">
        <Dropdown label="standard dropdown">
          <ul role="menu" aria-hidden="true">
            <li role="menuitem">
              <a href="#">Een link</a>
            </li>
            <li role="menuitem">
              <Link href="#">Een InertiaJS link</Link>
            </li>
            <li role="menuitem">
              <button type="button" v-on:click.prevent="toast.error('Dit gaat niet goed')">En een toast knop</button>
            </li>
            <li class="divider"></li>
            <li>
              <button type="button" class="danger">remove project</button>
            </li>
          </ul>
        </Dropdown>

        <VDropdown placement="bottom-start" :distance="20">
          <button type="button"><Icon name="eye" /> custom dropdown</button>

          <template #popper>
            <ul>
              <li>
                <a href="#">Een link</a>
              </li>
              <li>
                <Link href="#">Een InertiaJS link</Link>
              </li>
              <li>
                <button type="button" v-on:click.prevent="promiseToast('supermooi')">En een knop</button>
              </li>
            </ul>
          </template>
        </VDropdown>

        <Dropdown>
          <ul>
            <li>
              <a href="#">Een link</a>
            </li>
            <li>
              <Link href="#">Een hele lange link die gewoon maar door blijft gaan met tekst en van alles.</Link>
            </li>
            <li>
              <button type="button" v-on:click.prevent="toast.error('Dit gaat niet goed')">En een toast knop</button>
            </li>
          </ul>
        </Dropdown>
      </div>
    </section>

    <section>
      <header>
        <div>
          <h1>A curated list of all our icons</h1>
        </div>
      </header>

      <div class="icons-list">
        <div v-for="(icon, index) in icons" :key="index">
          <span class="icon">
            <Icon :name="icon" />
          </span>
          <span class="name">{{ icon }}</span>
        </div>
      </div>
    </section>

    <section>
      <header>
        <div>
          <h1>Prose</h1>
        </div>
      </header>

      <article class="prose">
        <h1>h1 title</h1>
        <p>
          Lorem ipsum dolor sit amet, <strong>consectetur</strong> adipisicing elit, sed do eiusmod tempor 1 2 3 4 5 6 7
          8 9 0 incididunt ut labore et <a href="#">dolore magna aliqua</a>.
        </p>

        <h2>h2 title</h2>
        <p>
          Lorem ipsum dolor sit amet, <strong>consectetur</strong> adipisicing elit, sed do eiusmod tempor incididunt ut
          labore et dolore magna aliqua. Curabitur blandit tempus porttitor. Praesent commodo cursus magna, vel
          scelerisque nisl consectetur et. Morbi leo risus, porta ac consectetur ac, vestibulum at eros. Etiam porta sem
          malesuada magna mollis euismod. Cras justo odio, dapibus ac facilisis in, egestas eget quam. Maecenas sed diam
          eget risus varius blandit sit amet non magna.
        </p>

        <ul>
          <li>Unordered list.</li>
          <li>Malesuada Sit Parturient Pellentesque Sollicitudin.</li>
          <li>
            Aenean eu leo quam. Pellentesque ornare sem lacinia quam venenatis vestibulum.<br /><strong
              >Nullam id dolor id nibh ultricies</strong
            >
            vehicula ut id elit.
          </li>
          <li>Donec sed odio dui. Curabitur blandit tempus porttitor.</li>
        </ul>

        <h3>h3 title</h3>
        <p>
          Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris.<br />
          Nisi ut aliquip ex ea commodo consequat. <q>Duis aute irure dolor in reprehenderit</q> in voluptate velit esse
          cillum dolore eu fugiat nulla pariatur.<br />
          Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
        </p>

        <blockquote>
          <p>
            Curabitur blandit tempus porttitor. Donec sed odio dui. Aenean eu leo quam. Pellentesque ornare sem lacinia
            quam venenatis vestibulum.
          </p>
          <cite>Someone somewhere</cite>
        </blockquote>

        <ol>
          <li>Ordered list.</li>
          <li>Malesuada Sit Parturient Pellentesque Sollicitudin.</li>
          <li>
            Aenean eu leo quam. Pellentesque ornare sem lacinia quam venenatis vestibulum.
            <strong>Nullam id dolor id nibh ultricies</strong> vehicula ut id elit.
          </li>
          <li>Donec sed odio dui. Curabitur blandit tempus porttitor.</li>
        </ol>

        <h4>h4 title</h4>
        <p>
          Praesent commodo cursus magna, vel scelerisque nisl consectetur et. Aenean lacinia
          <strong>bibendum</strong> nulla sed consectetur. <em>Nullam id dolor</em> id nibh ultricies vehicula ut id
          elit. Duis mollis, est non commodo luctus, nisi erat porttitor ligula, eget lacinia odio sem nec elit.
        </p>
      </article>
    </section>
  </main>
</template>

<script lang="ts" setup>
import { Head, Link } from '@inertiajs/vue3'
import { toast } from 'vue-sonner'

import DateTime from '/@admin:components/DateTime.vue'
import Dropdown from '/@admin:components/Dropdown.vue'
import FileInput from '/@admin:components/FileInput.vue'
import FormError from '/@admin:components/FormError.vue'
import Icon from '/@admin:components/Icon.vue'
import Pagination from '/@admin:components/Pagination.vue'
import ProgressCircle from '/@admin:components/ProgressCircle.vue'
import ProgressIndeterminate from '/@admin:components/ProgressIndeterminate.vue'
import { deleteConfirm } from '/@admin:composables/deleteConfirm'
import AuthLayout from '/@admin:layouts/Auth.vue'
import { wait } from '/@admin:shared/utils'

defineProps<{
  icons: string[]
}>()

defineOptions({
  layout: [AuthLayout],
})

const promiseToast = (text: string) => {
  toast.promise(() => new Promise((resolve) => setTimeout(resolve, 2500)), {
    loading: 'Loading',
    success: () => text,
    error: () => 'Error',
  })
}

const deleteItem = () => {
  deleteConfirm(async () => {
    await wait(1500)
    toast.success('Item deleted.')
  })
}
</script>

<style src="/@admin:css/views/styleguide.css"></style>
