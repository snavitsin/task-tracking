import tippy from 'tippy.js';

const defaults = {
  arrow: true,
  theme: 'light'
};

export default {
  bind(el, binding) {
    const type = typeof binding.value;
    const options = type === 'object' ? 
      binding.value : {content: binding.value};

    return tippy(el, {...defaults, ...options});
  },

  unbind(el) {
    el._tippy.destroy();
  } 
};