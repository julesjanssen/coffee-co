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
                    <td><span class="badge">23 November 2025</span></td>
                  </tr>
                  <tr>
                    <th>security support:</th>
                    <td><span class="badge">23 November 2026</span></td>
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
                <textarea rows="4" cols="40"></textarea>
                <p class="help">A piece of helptext for this input.</p>
              </div>
            </div>
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
              <label>radonly email:</label>
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
                <v-multifile-upload name="upload" />
              </div>
            </div>

            <div class="field">
              <label>image upload</label>
              <div>
                <v-multifile-upload name="image-upload" accept="image/*" />
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
            <td class="align-right">yes</td>
          </tr>

          <tr>
            <td>
              <a href="#">Jules Janssen</a>
            </td>
            <td>3 seconds ago</td>
            <td class="align-right">no</td>
          </tr>

          <tr class="trashed">
            <td>
              <a href="#">Karin Vrij</a>
            </td>
            <td>5 days ago</td>
            <td class="align-right">no</td>
          </tr>

          <tr>
            <td>
              <a href="#">Gianluca Riggio</a>
            </td>
            <td>12 years ago</td>
            <td class="align-right">no</td>
          </tr>
        </tbody>
      </table>

      <div class="pagination">
        <ul role="navigation">
          <li class="page-item">
            <a href="#" rel="prev" aria-label="vorige">&lsaquo;</a>
          </li>
          <li class="page-item">
            <a href="#">1</a>
          </li>
          <li class="page-item">
            <a href="#">2</a>
          </li>
          <li class="page-item active" aria-current="page">
            <span>3</span>
          </li>
          <li class="page-item">
            <a href="#">4</a>
          </li>
          <li class="page-item">
            <a href="#">5</a>
          </li>
          <li class="page-item">
            <a href="#">6</a>
          </li>
          <li class="page-item">
            <a href="#">7</a>
          </li>
          <li class="page-item">
            <a href="#">8</a>
          </li>
          <li class="page-item">
            <a href="#" rel="next" aria-label="volgende">&rsaquo;</a>
          </li>
        </ul>
      </div>
    </section>

    <section>
      <header>
        <div>
          <h1>A curated list of all our icons</h1>
          <h2>Step 4</h2>
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
  </main>
</template>

<script lang="ts" setup>
import { Head } from '@inertiajs/vue3'

import FormError from '/@admin:components/FormError.vue'
import Icon from '/@admin:components/Icon.vue'
import AuthLayout from '/@admin:layouts/Auth.vue'

defineProps<{
  icons: string[]
}>()

defineOptions({
  layout: [AuthLayout],
})
</script>

<style src="/@admin:css/views/styleguide.css"></style>
