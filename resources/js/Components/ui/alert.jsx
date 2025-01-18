// resources/js/Components/ui/alert.jsx
import React from 'react';

export const Alert = ({ children, variant = 'default', className = '', ...props }) => {
  const baseStyles = 'p-4 mb-4 rounded-lg';
  
  const variantStyles = {
    default: 'bg-blue-100 text-blue-800',
    destructive: 'bg-red-100 text-red-800',
    success: 'bg-green-100 text-green-800',
    warning: 'bg-yellow-100 text-yellow-800'
  };

  const styles = `${baseStyles} ${variantStyles[variant]} ${className}`;

  return (
    <div className={styles} role="alert" {...props}>
      {children}
    </div>
  );
};

export const AlertDescription = ({ children, className = '', ...props }) => {
  return (
    <div className={`text-sm ${className}`} {...props}>
      {children}
    </div>
  );
};