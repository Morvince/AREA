import React, { useEffect, useState } from 'react';
import { Dropdown } from 'react-native-element-dropdown';
import { useDropdownlistGetData } from '../../../api/apiCreatePage';
import styles from '../styles';
import { white } from '../../../color';

const data = [ //this is a temporary variable -> think to delete it after
  { name: 'Item 1', id: '1' },
  { name: 'Item 2', id: '2' },
  { name: 'Item 3', id: '3' },
  { name: 'Item 4', id: '4' },
  { name: 'Item 5', id: '5' },
  { name: 'Item 6', id: '6' },
  { name: 'Item 7', id: '7' },
  { name: 'Item 8', id: '8' },
];

const DropDownList = ({name, uri, defaultValue, saveDataDropdownlistCurrentAction}) => {
  const [value, setValue] = useState(defaultValue);
  const [isFocus, setIsFocus] = useState(false);
  // const [data, setData] = useState([])
  // const dropdownlistGetData = useDropdownlistGetData()

  // useEffect(() => {
  //   dropdownlistGetData.mutate(uri, {
  //     onSuccess: (data) => {
  //       setData(data.data.items[0])
  //     }
  //   })
  // }, [])

  return (
    <Dropdown
      style={[styles.sideBarDropdownlist, isFocus && { borderWidth: 2 }]}
      placeholderStyle={{fontSize: 16, color: white}}
      selectedTextStyle={{fontSize: 16, color: white}}
      inputSearchStyle={{height: 40, fontSize: 16}}
      iconStyle={{width: 20, height: 20, tintColor: white}}
      data={data}
      search
      maxHeight={300}
      labelField="name"
      valueField="id"
      placeholder={!isFocus ? name : '...'}
      searchPlaceholder="Search..."
      value={value}
      onFocus={() => setIsFocus(true)}
      onBlur={() => setIsFocus(false)}
      onChange={item => {
        setValue(item.id);
        saveDataDropdownlistCurrentAction(name, item.id)
        setIsFocus(false);
      }}
    />
  );
};

export default DropDownList;