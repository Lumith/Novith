<template>
  <div class="max-w-lg p-5 m-auto">
    <article class="p-5">
      <h1
        class="mb-2 text-6xl font-bold text-transparent bg-gradient-to-r bg-clip-text from-blue-500 to-green-500"
      >
        4Oh4
      </h1>
      <p class="font-bold">
        {{ $t('page_not_here_message') }}
        <router-link to="/dashboard" class="base-link">{{ $t('back_home') }}</router-link>.
      </p>
    </article>
  </div>
</template>

<script>
export default {
  name: "NotFound",
};
</script>
