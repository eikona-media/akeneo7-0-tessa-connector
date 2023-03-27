/**
 * @author    Yohan Blain <yohan.blain@akeneo.com>
 * @copyright 2017 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */
'use strict';

define([
  'jquery',
  'underscore',
  'pim/form/common/fields/field',
  'oro/translator',
  'eikona/tessa/form/common/fields/select-tags/template',
], function ($, _, BaseField, __, template) {
  return BaseField.extend({
    events: {
      'change input': function (event) {
        this.errors = [];
        this.updateModel(this.getFieldValue(event.target));
        // this.getRoot().render();
      },
    },
    template: _.template(template),

    /**
     * {@inheritdoc}
     */
    renderInput: function (templateContext) {
      if (undefined === this.getModelValue() && _.has(this.config, 'defaultValue')) {
        this.updateModel(this.config.defaultValue);
      }

      return this.template(
        _.extend(templateContext, {
          value: this.getModelValue(),
        })
      );
    },

    /**
     * {@inheritdoc}
     */
    postRender: function () {
      console.log('render');
      this.$('input.tessa-select2-tags').select2({
        tags: this.config.choices,
        tokenSeparators: [",", " "],
      });
    },

    /**
     * @param {Array} choices
     */
    formatChoices: function (choices) {
      return Array.isArray(choices) ? _.object(choices, choices) : _.mapObject(choices, __);
    },

    /**
     * {@inheritdoc}
     */
    getFieldValue: function (field) {
      const value = '' === $(field).val() ? null : $(field).val();

      return null === value ? [] : value.split(',');
    },

    getModelValue() {
      const value = BaseField.prototype.getModelValue.apply(this);
      return value ? value.join(',') : '';
    },
  });
});
