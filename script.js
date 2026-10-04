// script.js - client-side validation for the sign-in form
const $ = id => document.getElementById(id);

// Block invalid characters while typing
$('username').addEventListener('input', e => e.target.value = e.target.value.replace(/[^A-Za-z]/g, ''));
$('password').addEventListener('input', e => e.target.value = e.target.value.replace(/[^A-Za-z0-9]/g, ''));
$('phone').addEventListener('input', e => e.target.value = e.target.value.replace(/\D/g, ''));

// Show / hide password
$('toggle').addEventListener('click', () => {
  const p = $('password');
  const show = p.type === 'password';
  p.type = show ? 'text' : 'password';
  $('toggle').textContent = show ? 'Hide' : 'Show';
});

function setErr(id, msg) { $('e_' + id).textContent = msg; return msg === ''; }

function validate() {
  let ok = true;
  const u = $('username').value;
  ok = setErr('username', /^[A-Za-z]{1,30}$/.test(u) ? '' : 'Letters only, 1 to 30 characters.') && ok;

  const p = $('password').value;
  ok = setErr('password', /^[A-Za-z0-9]{1,8}$/.test(p) ? '' : 'Letters and numbers only, up to 8 characters.') && ok;

  const ph = $('phone').value;
  ok = setErr('phone', /^[0-9]{10}$/.test(ph) ? '' : 'Enter exactly 10 digits.') && ok;

  ok = setErr('city', $('city').value ? '' : 'Choose your city.') && ok;

  const cuis = [...$('cuisines').selectedOptions].length;
  ok = setErr('cuisines', cuis > 0 ? '' : 'Select at least one cuisine.') && ok;

  const g = document.querySelector('input[name="gender"]:checked');
  ok = setErr('gender', g ? '' : 'Select a gender.') && ok;

  ok = setErr('terms', $('terms').checked ? '' : 'Please accept the terms.') && ok;
  return ok;
}

// Form is submitted to PHP only if validation passes
$('signForm').addEventListener('submit', e => { if (!validate()) e.preventDefault(); });
