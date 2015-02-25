define(['react', 'classnames'], function (React, classNames) {
    'use strict';

    return React.createClass({
        render: function () {
            if (this.props.children) {
                var inputClasses = classNames(this.props.inputClasses);
                var text = this.props.children + ': ';

                return (
                    <strong className={inputClasses}>{text}</strong>
                );
            } else {
                return false;
            }
        }
    });
});