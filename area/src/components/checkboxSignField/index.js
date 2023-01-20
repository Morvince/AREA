import React from 'react';
import { LabelCheckboxField, CheckboxField } from './checkboxSignField';

const CheckboxSignField = ({label, children, onClick=null}) => {
  return (
    <div style={{display: "flex"}}>
      <CheckboxField type="checkbox" onClick={onClick}></CheckboxField>
      <LabelCheckboxField htmlFor={label}>{children}</LabelCheckboxField>
    </div>
  )
}

export default CheckboxSignField