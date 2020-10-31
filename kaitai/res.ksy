meta:
  id: res
  file-extension: res
  endian: le
  encoding: ascii
seq:
  - id: directory_offset
    type: u4
instances:
  directory:
    pos: directory_offset
    type: directory
types:
  directory:
    seq:
      - id: pad
        type: s4
      - id: sibling
        type: s4
      - id: entr_2
        type: u4
      - id: data_size
        type: u4
      - id: data_offset
        type: u4
      - id: name
        type: strz
      - id: padding
        size: (4 - _io.pos) % 4
      - id: child1
        if: pad != -1
        type: directory
    instances:
      sibling_dir:
        if: sibling != -1
        pos: sibling + _root.directory_offset
        type: directory
      data:
        if: entr_2 == 0
        pos: data_offset
        size: data_size 
