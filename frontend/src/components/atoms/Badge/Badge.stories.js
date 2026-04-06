import Badge from './Badge.vue';

export const Default = {
  args: {
    variant: 'default',
    children: 'Badge',
  },
  render: (args) => ({
    components: { Badge },
    setup() {
      return { args };
    },
    template: '<Badge v-bind="args">Badge</Badge>',
  }),
};
